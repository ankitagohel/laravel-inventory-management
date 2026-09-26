@extends('layouts.app')

@section('title', 'Stock Movement Ledger')
@section('page_title', 'Stock Movements & Audit Ledger')

@section('content')
    <!-- Filter & Action Bar -->
    <div class="filter-bar">
        <form action="{{ route('stock.index') }}" method="GET" class="filter-group" style="flex: 1;">
            <!-- Filter by Product -->
            <select name="product_id" class="form-control" style="width: auto; min-width: 220px;" onchange="this.form.submit()">
                <option value="">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->sku }} – {{ $p->name }}</option>
                @endforeach
            </select>

            <!-- Filter by Movement Type -->
            <select name="type" class="form-control" style="width: auto; min-width: 160px;" onchange="this.form.submit()">
                <option value="">All Types (IN/OUT/ADJ)</option>
                <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>Stock IN (Receiving)</option>
                <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>Stock OUT (Dispatch)</option>
                <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>Adjustment (Audit)</option>
            </select>

            <!-- Reference Number Search -->
            <div style="position: relative;">
                <input type="text" name="reference_no" value="{{ request('reference_no') }}" class="form-control" placeholder="Search PO / Ref #...">
            </div>

            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            @if(request()->anyFilled(['product_id', 'type', 'reference_no']))
                <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('export.movements') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                <span>Export Ledger CSV</span>
            </a>

            <button type="button" class="btn btn-primary btn-sm" onclick="openQuickStockModal()">
                <i data-lucide="plus-circle" style="width: 15px; height: 15px;"></i>
                <span>Record Movement</span>
            </button>
        </div>
    </div>

    <!-- Movement Ledger Table Panel -->
    <div class="panel">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Reference / PO #</th>
                        <th>Product & SKU</th>
                        <th>Movement Type</th>
                        <th>Quantity Change</th>
                        <th>Prev Stock</th>
                        <th>Resulting Stock</th>
                        <th>Reason / Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.82rem; white-space: nowrap;">
                                {{ $m->created_at->format('M d, Y H:i') }}
                                <div style="font-size: 0.72rem;">{{ $m->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="sku-badge">{{ $m->reference_no ?? 'MANUAL' }}</span>
                            </td>
                            <td>
                                @if($m->product)
                                    <a href="{{ route('products.show', $m->product) }}" style="color: var(--text-primary); text-decoration: none; font-weight: 600;">
                                        {{ $m->product->name }}
                                    </a>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $m->product->sku }}</div>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Deleted Product</span>
                                @endif
                            </td>
                            <td>
                                @if($m->type === 'IN')
                                    <span class="badge badge-success"><span class="badge-dot"></span> STOCK IN</span>
                                @elseif($m->type === 'OUT')
                                    <span class="badge badge-info"><span class="badge-dot"></span> STOCK OUT</span>
                                @else
                                    <span class="badge badge-warning"><span class="badge-dot"></span> ADJUSTMENT</span>
                                @endif
                            </td>
                            <td style="font-weight: 800; font-size: 0.95rem; color: {{ $m->type === 'IN' ? 'var(--success)' : ($m->type === 'OUT' ? 'var(--danger)' : 'var(--warning)') }};">
                                {{ $m->type === 'IN' ? '+' : ($m->type === 'OUT' ? '-' : '') }}{{ $m->quantity }}
                            </td>
                            <td style="color: var(--text-secondary);">{{ $m->previous_stock }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">{{ $m->resulting_stock }}</td>
                            <td style="color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $m->reason ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                No stock movements found matching current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-subtle);">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection
