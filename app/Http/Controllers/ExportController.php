<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export products catalog as CSV
     */
    public function exportProducts(): StreamedResponse
    {
        $fileName = 'inventory_catalog_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'ID',
                'SKU',
                'Barcode',
                'Product Name',
                'Category',
                'Supplier',
                'Cost Price (₹)',
                'Selling Price (₹)',
                'Current Stock',
                'Unit',
                'Min Stock Alert',
                'Stock Status',
                'Total Cost Valuation (₹)',
                'Total Retail Valuation (₹)',
                'Location',
            ]);

            Product::with(['category', 'supplier'])->chunk(100, function ($products) use ($handle) {
                foreach ($products as $p) {
                    fputcsv($handle, [
                        $p->id,
                        $p->sku,
                        $p->barcode,
                        $p->name,
                        $p->category?->name ?? 'Uncategorized',
                        $p->supplier?->name ?? 'None',
                        number_format($p->cost_price, 2, '.', ''),
                        number_format($p->selling_price, 2, '.', ''),
                        $p->current_stock,
                        $p->unit,
                        $p->min_stock_alert,
                        $p->stock_status_label,
                        number_format($p->total_cost_value, 2, '.', ''),
                        number_format($p->total_retail_value, 2, '.', ''),
                        $p->warehouse_location ?? 'N/A',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Export stock movement audit trail as CSV
     */
    public function exportMovements(Request $request): StreamedResponse
    {
        $fileName = 'stock_movement_ledger_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'Log ID',
                'Timestamp',
                'Reference No',
                'SKU',
                'Product Name',
                'Movement Type',
                'Quantity Changed',
                'Previous Stock',
                'Resulting Stock',
                'Reason / Notes',
            ]);

            StockMovement::with('product')->orderBy('id', 'desc')->chunk(100, function ($movements) use ($handle) {
                foreach ($movements as $m) {
                    fputcsv($handle, [
                        $m->id,
                        $m->created_at->format('Y-m-d H:i:s'),
                        $m->reference_no ?? 'N/A',
                        $m->product?->sku ?? 'DELETED',
                        $m->product?->name ?? 'DELETED',
                        $m->type,
                        $m->quantity,
                        $m->previous_stock,
                        $m->resulting_stock,
                        $m->reason ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
