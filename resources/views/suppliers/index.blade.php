@extends('layouts.app')

@section('title', 'Suppliers & Vendors')
@section('page_title', 'Supplier Directory & Contacts')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
        <p style="color: var(--text-secondary); font-size: 0.95rem;">Manage approved vendors, distributors, and contact details.</p>
        <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addSupplierModal')">
            <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
            <span>Add Supplier</span>
        </button>
    </div>

    <!-- Supplier Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($suppliers as $supplier)
            <div class="panel" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div class="panel-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="brand-icon" style="width: 32px; height: 32px; font-size: 0.85rem; background: linear-gradient(135deg, var(--info), #3b82f6);">
                            <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700;">{{ $supplier->name }}</h3>
                            <span class="badge badge-success" style="font-size: 0.7rem;">Active Vendor</span>
                        </div>
                    </div>

                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Remove this supplier?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon" style="color: var(--danger);" title="Delete Supplier">
                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        </button>
                    </form>
                </div>

                <div class="panel-body" style="flex: 1; display: flex; flex-direction: column; gap: 10px;">
                    @if($supplier->contact_person)
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem;">
                            <i data-lucide="user" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
                            <span>{{ $supplier->contact_person }}</span>
                        </div>
                    @endif

                    @if($supplier->email)
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem;">
                            <i data-lucide="mail" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
                            <a href="mailto:{{ $supplier->email }}" style="color: var(--primary); text-decoration: none;">{{ $supplier->email }}</a>
                        </div>
                    @endif

                    @if($supplier->phone)
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem;">
                            <i data-lucide="phone" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
                            <span style="color: var(--text-secondary);">{{ $supplier->phone }}</span>
                        </div>
                    @endif

                    @if($supplier->address)
                        <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 0.82rem; color: var(--text-muted);">
                            <i data-lucide="map-pin" style="width: 15px; height: 15px; margin-top: 2px;"></i>
                            <span>{{ $supplier->address }}</span>
                        </div>
                    @endif

                    <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.78rem; color: var(--text-muted);">Supplies:</span>
                        <span style="font-weight: 700; font-size: 0.95rem;">{{ $supplier->products_count }} Catalog Items</span>
                    </div>
                </div>

                <div style="padding: 12px 20px; background: var(--bg-surface-elevated); border-top: 1px solid var(--border-subtle); display: flex; justify-content: flex-end;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm" style="width: 100%;">
                        <span>View Supplied Products</span>
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 50px;">
                No suppliers registered yet. Click "Add Supplier" to register one.
            </div>
        @endforelse
    </div>

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Register New Supplier</h3>
                <button type="button" class="btn-icon" onclick="closeModal('addSupplierModal')">&times;</button>
            </div>
            
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Company / Supplier Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Apex Tech Supplies">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="e.g. Sarah Jenkins">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="sales@vendor.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Office / Warehouse Address</label>
                        <textarea name="address" rows="2" class="form-control" placeholder="Street, City, State, ZIP"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addSupplierModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
@endsection
