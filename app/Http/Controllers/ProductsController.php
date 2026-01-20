<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function search()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view("dashboard.products.create", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => 'required|string|max:255',
            "author" => "sometimes|string|max:255",
            "category" => "required|exists:categories,id",
            "price" => "required|numeric",
            "stock" => "required|numeric",
            "description" => 'nullable|string|max:500',
            'img' => 'nullable', // Validasi file gambar
        ]);

        $productData = [
            "title" => $request->title,
            "author" => $request->author,
            "seller_id" => Auth::user()->id,
            "category_id" => $request->category,
            "price" => $request->price,
            "stock" => $request->stock,
            "description" => $request->description,
            "img" => null
        ];


        if ($request->img && is_string($request->img) && str_starts_with($request->img, 'data:image')) {

            preg_match('/^data:image\/(\w+);base64,/', $request->img, $matches);
            $extension = $matches[1] ?? 'png';

            $imageBase64 = substr($request->img, strpos($request->img, ',') + 1);
            $imageBase64 = base64_decode($imageBase64);

            if ($imageBase64 === false) {
                return response()->json(['message' => 'Base64 tidak valid'], 400);
            }

            $fileName = Str::slug($request->title) . '-' . time() . '.' . $extension;
            Storage::disk('public')->put('products/' . $fileName, $imageBase64);

            $productData['img'] = $fileName;
        }

        /** =========================
         * HANDLE FILE UPLOAD
         * ========================= */ elseif ($request->hasFile('img')) {

            $request->validate([
                'img' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
            ]);

            $file = $request->file('img');
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $fileName, 'public');

            $productData['img'] = $fileName;
        }

        $product = Product::create($productData);

        // 5. Cek jika request adalah AJAX (untuk menyesuaikan respon JavaScript Anda)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'product created successfully.',
                'data' => $product
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $products)
    {
        //
    }
}
