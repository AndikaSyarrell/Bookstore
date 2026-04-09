<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Quick search for autocomplete
     */
    public function quickSearch(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'total_count' => 0
            ]);
        }

        // Search products (limit to 5 for quick results)
        $products = Product::with(['seller', 'category'])
            ->where('stock', '>', 0)
            ->whereHas('seller.bankAccounts') // Pastikan seller memiliki bank account
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('seller', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'author' => $product->author,
                    'price' => $product->selling_price,
                    'img' => $product->img,
                    'category_name' => $product->category->name ?? 'Uncategorized',
                    'seller_name' => $product->seller->name ?? 'Unknown',
                ];
            });

        // Get total count
        $totalCount = Product::where('stock', '>', 0)
            ->whereHas('seller.bankAccounts') // Pastikan seller memiliki bank account
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->count();

        return response()->json([
            'products' => $products,
            'total_count' => $totalCount
        ]);
    }

    /**
     * Get categories for quick browse
     */
    public function getCategories()
    {
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('stock', '>', 0);
        }])
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->products_count,
                ];
            });

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Search products - Returns to dedicated search results page
     */
    public function search(Request $request)
    {
        $query = Product::with(['seller', 'category'])
            ->where('stock', '>', 0)
            ->whereHas('seller.bankAccounts');

        $searchQuery = $request->input('q', '');
        $filters = [
            'query' => $searchQuery,
            'category' => $request->input('category'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'sort' => $request->input('sort', 'newest'),
        ];

        // Search by keyword
        if (!empty($searchQuery)) {
            // 1. Pastikan SELLER memiliki BANK ACCOUNT (Syarat Wajib)
            $query->whereHas('seller.bankAccounts');

            // 2. Kelompokkan pencarian kata kunci (Syarat Opsional di dalam)
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('author', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                    ->orWhereHas('category', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%");
                    })
                    ->orWhereHas('seller', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%");
                    });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        switch ($filters['sort']) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'popular':
                $query->withCount(['orderDetails as total_sold' => function ($q) {
                    $q->select(DB::raw('COALESCE(SUM(quantity), 0)'));
                }])->orderBy('total_sold', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get results with pagination
        $products = $query->paginate(12)->appends($request->all());

        // Get total results count
        $totalResults = $products->total();

        // All categories for filter
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('stock', '>', 0);
        }])
            ->having('products_count', '>', 0)
            ->orderBy('name', 'asc')
            ->get();

        // Price range stats
        $priceStats = Product::where('stock', '>', 0)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('buyer.search.index', compact(
            'products',
            'categories',
            'filters',
            'totalResults',
            'priceStats'
        ));
    }

    /**
     * Track search click (optional analytics)
     */
    public function trackClick(Request $request)
    {
        // Optional: Store in analytics table or log
        // For now, just return success

        return response()->json(['success' => true]);
    }
}
