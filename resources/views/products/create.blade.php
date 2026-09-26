@extends('layouts.app')

@section('title', 'Add New Product')
@section('page_title', 'Create New Catalog Item')

@section('content')
    <div style="max-width: 900px; margin: 0 auto; width: 100%;">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i data-lucide="package-plus" style="color: var(--primary);"></i>
                    <span>Product Specifications</span>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Back to Catalog</span>
                </a>
            </div>

            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="panel-body">
                    <!-- General Details -->
                    <h4 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 16px;">General Information</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="e.g. Logitech MX Master 3S">
                        </div>

                        <div class="form-group">
                            <label class="form-label">SKU (Stock Keeping Unit) *</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="form-control" required placeholder="e.g. ACC-LOGI-MX3S" style="text-transform: uppercase;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Barcode / UPC / EAN</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-control" placeholder="e.g. 890123456789">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Primary Supplier</label>
                            <select name="supplier_id" class="form-control">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Pricing & Inventory Section -->
                    <h4 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin: 24px 0 16px;">Pricing & Inventory Setup</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cost Price (₹) *</label>
                            <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', '0.00') }}" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Selling Price (₹) *</label>
                            <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', '0.00') }}" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Initial Stock Quantity</label>
                            <input type="number" min="0" name="initial_stock" value="{{ old('initial_stock', '0') }}" class="form-control" placeholder="0">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Low Stock Alert Threshold *</label>
                            <input type="number" min="0" name="min_stock_alert" value="{{ old('min_stock_alert', '10') }}" class="form-control" required placeholder="10">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Unit of Measurement *</label>
                            <select name="unit" class="form-control" required>
                                <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box / Pack</option>
                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                <option value="meters" {{ old('unit') == 'meters' ? 'selected' : '' }}>Meters (m)</option>
                                <option value="liters" {{ old('unit') == 'liters' ? 'selected' : '' }}>Liters (L)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Warehouse / Bin Location</label>
                            <input type="text" name="warehouse_location" value="{{ old('warehouse_location') }}" class="form-control" placeholder="e.g. Aisle 3, Rack B-04">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description / Specifications</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Enter product details, specifications, warranty notes...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="panel-header" style="background: var(--bg-surface-elevated); border-top: 1px solid var(--border-subtle); justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                        <span>Save & Create Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
