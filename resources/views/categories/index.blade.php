@extends('layouts.app')

@section('title', 'Categories Management')
@section('page_title', 'Product Categories & Classification')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
        <p style="color: var(--text-secondary); font-size: 0.95rem;">Group and classify items across your warehouse and supply chain.</p>
        <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addCategoryModal')">
            <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
            <span>Add Category</span>
        </button>
    </div>

    <!-- Category Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($categories as $category)
            <div class="panel" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div class="panel-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="brand-icon" style="width: 32px; height: 32px; font-size: 0.85rem;">
                            <i data-lucide="folder" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700;">{{ $category->name }}</h3>
                            @if($category->code)
                                <span class="sku-badge">{{ $category->code }}</span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category? Products in this category will become Uncategorized.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon" style="color: var(--danger);" title="Delete Category">
                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        </button>
                    </form>
                </div>

                <div class="panel-body" style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.88rem; min-height: 42px;">
                        {{ $category->description ?: 'No category description provided.' }}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border-subtle);">
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Items</span>
                            <div style="font-weight: 700; font-size: 1.1rem;">{{ $category->products_count }} Products</div>
                        </div>

                        @php
                            $catStockValue = $category->products->sum(fn($p) => $p->current_stock * $p->cost_price);
                        @endphp
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Stock Valuation</span>
                            <div style="font-weight: 700; font-size: 1.1rem; color: var(--success);">₹{{ number_format($catStockValue, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div style="padding: 12px 20px; background: var(--bg-surface-elevated); border-top: 1px solid var(--border-subtle); display: flex; justify-content: flex-end;">
                    <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="btn btn-secondary btn-sm" style="width: 100%;">
                        <span>View {{ $category->products_count }} Products</span>
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 50px;">
                No categories found. Click "Add Category" to create your first one.
            </div>
        @endforelse
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Create New Category</h3>
                <button type="button" class="btn-icon" onclick="closeModal('addCategoryModal')">&times;</button>
            </div>
            
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Storage Devices">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category Code / Prefix</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. STOR" style="text-transform: uppercase;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Brief summary of items in this category..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addCategoryModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
