<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('current_stock', '>', 0)
                      ->whereColumn('current_stock', '<=', 'min_stock_alert');
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereColumn('current_stock', '>', 'min_stock_alert');
            }
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = ['name', 'current_stock', 'cost_price', 'selling_price', 'created_at', 'sku'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'initial_stock' => 'nullable|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'warehouse_location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $initialStock = $validated['initial_stock'] ?? 0;

        DB::transaction(function () use ($validated, $initialStock) {
            $product = Product::create([
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'barcode' => $validated['barcode'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'current_stock' => $initialStock,
                'min_stock_alert' => $validated['min_stock_alert'],
                'unit' => $validated['unit'],
                'warehouse_location' => $validated['warehouse_location'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]);

            if ($initialStock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'IN',
                    'quantity' => $initialStock,
                    'previous_stock' => 0,
                    'resulting_stock' => $initialStock,
                    'reason' => 'Initial Inventory Intake',
                    'reference_no' => 'INIT-' . $product->sku,
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'stockMovements' => function ($q) {
            $q->latest()->take(25);
        }]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'warehouse_location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,draft,archived',
        ]);

        $validated['sku'] = strtoupper($validated['sku']);

        $product->update($validated);

        return redirect()->route('products.show', $product)->with('success', 'Product details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')->with('success', "Product '{$name}' was deleted successfully.");
    }
}
