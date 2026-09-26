@extends('layouts.app')

@section('title', $product->name . ' (' . $product->sku . ')')
@section('page_title', 'Product Details: ' . $product->name)

@section('content')
    <!-- Product Header Card -->
    <div class="panel" style="margin-bottom: 24px;">
        <div class="panel-header" style="flex-wrap: wrap; gap: 14px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                    <span class="sku-badge" style="font-size: 0.95rem;">{{ $product->sku }}</span>
                    <span class="badge {{ $product->stock_status_badge_class }}">
                        <span class="badge-dot"></span>
                        {{ $product->stock_status_label }}
                    </span>
                    <span class="badge badge-info">{{ ucfirst($product->status) }}</span>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800;">{{ $product->name }}</h2>
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="button" class="btn btn-primary" onclick="openQuickStockModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->current_stock }}, 'IN')">
                    <i data-lucide="arrow-left-right" style="width: 16px; height: 16px;"></i>
                    <span>Adjust / Restock</span>
                </button>

                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary">
                    <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                    <span>Edit</span>
                </a>

                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Catalog</span>
                </a>
            </div>
        </div>

        <div class="panel-body">
            <!-- Product KPIs -->
            <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
                <div class="kpi-card kpi-indigo" style="padding: 16px;">
                    <div class="kpi-label">Current Stock</div>
                    <div class="kpi-value" style="font-size: 1.6rem; color: {{ $product->current_stock <= 0 ? 'var(--danger)' : ($product->current_stock <= $product->min_stock_alert ? 'var(--warning)' : 'var(--text-primary)') }};">
                        {{ $product->current_stock }} {{ $product->unit }}
                    </div>
                    <div class="kpi-sub">Alert trigger at {{ $product->min_stock_alert }} {{ $product->unit }}</div>
                </div>

                <div class="kpi-card kpi-emerald" style="padding: 16px;">
                    <div class="kpi-label">Pricing & Unit Margin</div>
                    <div class="kpi-value" style="font-size: 1.6rem;">
                        ₹{{ number_format($product->selling_price, 2) }}
                    </div>
                    @php
                        $margin = $product->selling_price > 0 ? (($product->selling_price - $product->cost_price) / $product->selling_price) * 100 : 0;
                    @endphp
                    <div class="kpi-sub">Cost: ₹{{ number_format($product->cost_price, 2) }} ({{ number_format($margin, 1) }}% Margin)</div>
                </div>

                <div class="kpi-card kpi-sky" style="padding: 16px;">
                    <div class="kpi-label">Total Cost Valuation</div>
                    <div class="kpi-value" style="font-size: 1.6rem;">
                        ₹{{ number_format($product->total_cost_value, 2) }}
                    </div>
                    <div class="kpi-sub">Retail value: ₹{{ number_format($product->total_retail_value, 2) }}</div>
                </div>

                <div class="kpi-card kpi-amber" style="padding: 16px;">
                    <div class="kpi-label">Warehouse Bin</div>
                    <div class="kpi-value" style="font-size: 1.35rem; font-weight: 700;">
                        {{ $product->warehouse_location ?? 'Not Assigned' }}
                    </div>
                    <div class="kpi-sub">UPC: {{ $product->barcode ?? 'None' }}</div>
                </div>
            </div>

            <!-- Meta details -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; background: var(--bg-surface-elevated); padding: 18px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Category:</span>
                    <div style="font-weight: 600;">{{ $product->category?->name ?? 'Uncategorized' }}</div>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Supplier:</span>
                    <div style="font-weight: 600;">{{ $product->supplier?->name ?? 'None' }}</div>
                    @if($product->supplier?->email)
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $product->supplier->email }}</div>
                    @endif
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Barcode / EAN:</span>
                    <div style="font-weight: 600; font-family: 'JetBrains Mono', monospace;">{{ $product->barcode ?? 'N/A' }}</div>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Created On:</span>
                    <div style="font-weight: 600;">{{ $product->created_at->format('M d, Y') }}</div>
                </div>
            </div>

            @if($product->description)
                <div style="margin-top: 18px;">
                    <h4 style="font-size: 0.82rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Specifications & Description</h4>
                    <p style="color: var(--text-secondary); font-size: 0.92rem; line-height: 1.6;">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Stock Movement History Ledger For This Product -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <i data-lucide="history" style="color: var(--primary);"></i>
                <span>Historical Stock Movements (Audit Trail)</span>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="openQuickStockModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->current_stock }}, 'IN')">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                <span>Add Movement</span>
            </button>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Quantity Change</th>
                        <th>Previous Stock</th>
                        <th>Resulting Stock</th>
                        <th>Reference / PO</th>
                        <th>Reason / Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->stockMovements as $movement)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.82rem;">
                                {{ $movement->created_at->format('M d, Y H:i') }}
                                <div style="font-size: 0.75rem;">({{ $movement->created_at->diffForHumans() }})</div>
                            </td>
                            <td>
                                @if($movement->type === 'IN')
                                    <span class="badge badge-success"><span class="badge-dot"></span> STOCK IN</span>
                                @elseif($movement->type === 'OUT')
                                    <span class="badge badge-info"><span class="badge-dot"></span> STOCK OUT</span>
                                @else
                                    <span class="badge badge-warning"><span class="badge-dot"></span> AUDIT ADJUSTMENT</span>
                                @endif
                            </td>
                            <td style="font-weight: 700; font-size: 1rem; color: {{ $movement->type === 'IN' ? 'var(--success)' : ($movement->type === 'OUT' ? 'var(--danger)' : 'var(--warning)') }};">
                                {{ $movement->type === 'IN' ? '+' : ($movement->type === 'OUT' ? '-' : '') }}{{ $movement->quantity }}
                            </td>
                            <td style="color: var(--text-muted);">{{ $movement->previous_stock }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">{{ $movement->resulting_stock }}</td>
                            <td>
                                <span class="sku-badge">{{ $movement->reference_no ?? 'N/A' }}</span>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $movement->reason ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                No stock movements recorded for this item yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
