<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->with(['products' => function ($q) {
                $q->select('category_id', 'current_stock', 'cost_price');
            }])
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
