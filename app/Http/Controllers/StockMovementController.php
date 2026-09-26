<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Display the stock movements ledger.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('reference_no')) {
            $query->where('reference_no', 'like', '%' . $request->reference_no . '%');
        }

        $movements = $query->latest()->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('stock.index', compact('movements', 'products'));
    }

    /**
     * Record a new stock movement (Stock In, Stock Out, or Audit Adjustment).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Lock product for atomic updates
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
                $prevStock = $product->current_stock;
                $qty = (int) $validated['quantity'];
                $type = $validated['type'];

                if ($type === 'IN') {
                    $newStock = $prevStock + $qty;
                } elseif ($type === 'OUT') {
                    if ($prevStock < $qty) {
                        throw new \Exception("Insufficient stock! Available: {$prevStock}, requested: {$qty}.");
                    }
                    $newStock = $prevStock - $qty;
                } elseif ($type === 'ADJUSTMENT') {
                    // For adjustment, quantity represents the actual counted inventory
                    $newStock = $qty;
                    $qty = $newStock - $prevStock; // Delta
                } else {
                    throw new \Exception("Invalid movement type.");
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => $type,
                    'quantity' => abs($qty),
                    'previous_stock' => $prevStock,
                    'resulting_stock' => $newStock,
                    'reason' => $validated['reason'] ?? ($type . ' Transaction'),
                    'reference_no' => !empty($validated['reference_no']) ? $validated['reference_no'] : (strtoupper($type) . '-' . time()),
                ]);

                $product->current_stock = $newStock;
                $product->save();
            });

            return back()->with('success', 'Stock movement recorded successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }
    }
}
