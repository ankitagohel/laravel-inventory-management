@extends('layouts.app')

@section('title', 'Edit Product - ' . $product->name)
@section('page_title', 'Update Catalog Item: ' . $product->name)

@section('content')
    <div style="max-width: 900px; margin: 0 auto; width: 100%;">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i data-lucide="edit-3" style="color: var(--primary);"></i>
                    <span>Editing Product: {{ $product->sku }}</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-secondary btn-sm">View Item</a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Back to Catalog</a>
                </div>
            </div>

            <form action="{{ route('products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="panel-body">
                    <h4 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 16px;">General Information</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">SKU *</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control" required style="text-transform: uppercase;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Barcode / UPC / EAN</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Primary Supplier</label>
                            <select name="supplier_id" class="form-control">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Pricing & Inventory Section -->
                    <h4 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin: 24px 0 16px;">Pricing & Inventory Setup</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cost Price (₹) *</label>
                            <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Selling Price (₹) *</label>
                            <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Low Stock Alert Threshold *</label>
                            <input type="number" min="0" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Catalog Status *</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Unit of Measurement *</label>
                            <select name="unit" class="form-control" required>
                                <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                <option value="box" {{ old('unit', $product->unit) == 'box' ? 'selected' : '' }}>Box / Pack</option>
                                <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                <option value="meters" {{ old('unit', $product->unit) == 'meters' ? 'selected' : '' }}>Meters (m)</option>
                                <option value="liters" {{ old('unit', $product->unit) == 'liters' ? 'selected' : '' }}>Liters (L)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Warehouse / Bin Location</label>
                            <input type="text" name="warehouse_location" value="{{ old('warehouse_location', $product->warehouse_location) }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description / Specifications</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <div class="panel-header" style="background: var(--bg-surface-elevated); border-top: 1px solid var(--border-subtle); justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
