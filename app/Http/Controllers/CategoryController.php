<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $categories = Category::query();

        // Filter berdasarkan teks pencarian
        if ($request->filled('search')) {
            $categories->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan keberadaan relasi produk
        if ($request->filled('filter')) {
            if ($request->filter === 'exist') {
                $categories->has('products'); // Memiliki minimal 1 produk
            } elseif ($request->filter === 'empty') {
                $categories->doesntHave('products'); // Tidak memiliki produk sama sekali
            }
        }

        // Gunakan withQueryString() agar filter pencarian tidak hilang saat pindah halaman paginasi
        $categories = $categories->paginate(5)->withQueryString();

        return view('dashboard.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'The category name is required.',
            'name.unique' => 'The category name must be unique.',
            'slug.required' => 'The category slug is required.',
            'slug.unique' => 'The category slug must be unique.',
        ]);

        // dd($request->all());

        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $Category) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $Category) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $Category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $Category->id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $Category->id,
        ], [
            'name.required' => 'The category name is required.',
            'name.unique' => 'The category name must be unique.',
            'slug.required' => 'The category slug is required.',
            'slug.unique' => 'The category slug must be unique.',
        ]);

        $Category->update(
            $request->only(['name', 'slug'])
        );

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $Category)
    {
        // Check if category has products
        if ($Category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with products. Please delete all products first.');
        }

        $Category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
