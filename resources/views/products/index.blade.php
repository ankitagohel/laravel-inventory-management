@extends('layouts.app')

@section('title', 'Product Catalog')
@section('page_title', 'Products & Inventory Catalog')

@section('content')
    <!-- Search & Filters Bar -->
    <div class="filter-bar">
        <form action="{{ route('products.index') }}" method="GET" class="filter-group" style="flex: 1;">
            <!-- Keyword Search -->
            <div style="position: relative; flex: 1; min-width: 200px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by SKU, Product Name, Barcode..." style="padding-left: 36px;">
                <i data-lucide="search" style="position: absolute; left: 12px; top: 12px; width: 16px; height: 16px; color: var(--text-muted);"></i>
            </div>

            <!-- Category Filter -->
            <select name="category_id" class="form-control" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Stock Status Filter -->
            <select name="stock_status" class="form-control" style="width: auto; min-width: 150px;" onchange="this.form.submit()">
                <option value="">All Stock Levels</option>
                <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Warning</option>
                <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>

            <!-- Sorting -->
            <select name="sort" class="form-control" style="width: auto; min-width: 140px;" onchange="this.form.submit()">
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Newest First</option>
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                <option value="current_stock" {{ request('sort') === 'current_stock' ? 'selected' : '' }}>Stock Quantity</option>
                <option value="selling_price" {{ request('sort') === 'selling_price' ? 'selected' : '' }}>Price</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            @if(request()->anyFilled(['search', 'category_id', 'stock_status', 'sort']))
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm" title="Clear all filters">Reset</a>
            @endif
        </form>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('export.products') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                <span>Export CSV</span>
            </a>
            @if(auth()->check() && auth()->user()->canManageProducts())
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Add Product</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Products Table Panel -->
    <div class="panel">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>SKU / Barcode</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Selling Price</th>
                        <th>Stock Level</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div><span class="sku-badge">{{ $product->sku }}</span></div>
                                @if($product->barcode)
                                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">{{ $product->barcode }}</div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}" style="color: var(--text-primary); text-decoration: none; font-weight: 600;">
                                    {{ $product->name }}
                                </a>
                                @if($product->warehouse_location)
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">Loc: {{ $product->warehouse_location }}</div>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $product->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td style="color: var(--text-muted);">₹{{ number_format($product->cost_price, 2) }}</td>
                            <td style="font-weight: 600; color: var(--text-primary);">₹{{ number_format($product->selling_price, 2) }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 700; font-size: 0.95rem; min-width: 32px;">
                                        {{ $product->current_stock }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $product->unit }}</span>
                                </div>
                                <div class="progress-bar-container" style="margin-top: 4px;">
                                    @php
                                        $percent = min(100, max(5, ($product->current_stock / max(1, $product->min_stock_alert * 3)) * 100));
                                        $barColor = $product->current_stock <= 0 ? 'var(--danger)' : ($product->current_stock <= $product->min_stock_alert ? 'var(--warning)' : 'var(--success)');
                                    @endphp
                                    <div class="progress-bar-fill" style="width: {{ $percent }}%; background: {{ $barColor }};"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $product->stock_status_badge_class }}">
                                    <span class="badge-dot"></span>
                                    {{ $product->stock_status_label }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                    <!-- Quick Stock Action -->
                                    @if(auth()->check() && auth()->user()->canRecordMovements())
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="openQuickStockModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->current_stock }}, 'IN')" title="Adjust Stock">
                                            <i data-lucide="arrow-left-right" style="width: 14px; height: 14px;"></i>
                                            <span>Stock</span>
                                        </button>
                                    @endif

                                    <!-- View Details -->
                                    <a href="{{ route('products.show', $product) }}" class="btn-icon" title="View Details">
                                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                    </a>

                                    <!-- Edit -->
                                    @if(auth()->check() && auth()->user()->canManageProducts())
                                        <a href="{{ route('products.edit', $product) }}" class="btn-icon" title="Edit Product">
                                            <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                                        </a>
                                    @endif

                                    <!-- Delete -->
                                    @if(auth()->check() && auth()->user()->canDelete())
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon" style="color: var(--danger);" title="Delete Product">
                                                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                No products found matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-subtle);">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
