<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – {{ config('app.name', 'StockMaster IMS') }}</title>
    
    <!-- Design & Styling -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Chart.js for Live Data Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i data-lucide="boxes"></i>
            </div>
            <div class="brand-text">
                <h2>StockMaster</h2>
                <span>Inventory System</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Core Modules</div>
            
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i data-lucide="package"></i>
                <span>Products Catalog</span>
            </a>

            <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                <i data-lucide="arrow-left-right"></i>
                <span>Stock Movements</span>
            </a>

            <div class="nav-section-title" style="margin-top: 14px;">Organization</div>

            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i data-lucide="folder-tree"></i>
                <span>Categories</span>
            </a>

            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i data-lucide="truck"></i>
                <span>Suppliers</span>
            </a>

            <div class="nav-section-title" style="margin-top: 14px;">Reports & Exports</div>

            <a href="{{ route('export.products') }}" class="nav-link" title="Export All Products to CSV">
                <i data-lucide="file-spreadsheet"></i>
                <span>Export Catalog CSV</span>
            </a>

            <a href="{{ route('export.movements') }}" class="nav-link" title="Export Stock Ledger to CSV">
                <i data-lucide="history"></i>
                <span>Export Ledger CSV</span>
            </a>

            @if(auth()->check() && auth()->user()->canManageUsers())
                <div class="nav-section-title" style="margin-top: 14px;">Administration</div>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i data-lucide="shield-check" style="color: var(--danger);"></i>
                    <span>Access & User Roles</span>
                </a>
            @endif
        </nav>

        <!-- Current User Profile in Sidebar Footer -->
        <div class="sidebar-footer" style="flex-direction: column; align-items: stretch; gap: 10px;">
            @if(auth()->check())
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="overflow: hidden;">
                        <div style="font-weight: 700; font-size: 0.82rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; color: var(--text-primary);">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                    <span class="badge {{ auth()->user()->role_badge_class }}" style="font-size: 0.65rem; padding: 2px 6px;">
                        {{ strtoupper(auth()->user()->role) }}
                    </span>
                </div>
            @endif
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-subtle); padding-top: 8px;">
                <span class="badge badge-success"><span class="badge-dot"></span> MySQL Connected</span>
                <span style="font-size: 0.72rem; color: var(--text-muted);">v1.0</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="app-main">
        <!-- Topbar -->
        <header class="app-topbar">
            <h1 class="topbar-title">@yield('page_title', 'Inventory Overview')</h1>

            <div class="topbar-actions">
                <!-- Fast Role Switcher Pill Bar for Testing -->
                <div style="display: flex; align-items: center; gap: 6px; background: var(--bg-surface); padding: 4px 8px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); font-size: 0.78rem;">
                    <span style="color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="user-check" style="width: 14px; height: 14px;"></i> Role:
                    </span>
                    <a href="{{ route('login.quick', 'admin') }}" class="btn-sm" style="padding: 2px 8px; border-radius: 4px; text-decoration: none; font-weight: 700; color: {{ auth()->user()?->role === 'admin' ? '#fff' : 'var(--text-muted)' }}; background: {{ auth()->user()?->role === 'admin' ? 'var(--danger)' : 'transparent' }};">Admin</a>
                    <a href="{{ route('login.quick', 'manager') }}" class="btn-sm" style="padding: 2px 8px; border-radius: 4px; text-decoration: none; font-weight: 700; color: {{ auth()->user()?->role === 'manager' ? '#fff' : 'var(--text-muted)' }}; background: {{ auth()->user()?->role === 'manager' ? 'var(--info)' : 'transparent' }};">Manager</a>
                    <a href="{{ route('login.quick', 'staff') }}" class="btn-sm" style="padding: 2px 8px; border-radius: 4px; text-decoration: none; font-weight: 700; color: {{ auth()->user()?->role === 'staff' ? '#fff' : 'var(--text-muted)' }}; background: {{ auth()->user()?->role === 'staff' ? 'var(--success)' : 'transparent' }};">Staff</a>
                    <a href="{{ route('login.quick', 'auditor') }}" class="btn-sm" style="padding: 2px 8px; border-radius: 4px; text-decoration: none; font-weight: 700; color: {{ auth()->user()?->role === 'auditor' ? '#fff' : 'var(--text-muted)' }}; background: {{ auth()->user()?->role === 'auditor' ? 'var(--warning)' : 'transparent' }};">Auditor</a>
                </div>

                @if(auth()->check() && auth()->user()->canRecordMovements())
                    <button type="button" class="btn btn-primary btn-sm" onclick="openQuickStockModal()">
                        <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                        <span>Record Movement</span>
                    </button>
                @endif

                @if(auth()->check() && auth()->user()->canManageProducts())
                    <a href="{{ route('products.create') }}" class="btn btn-secondary btn-sm">
                        <i data-lucide="package-plus" style="width: 16px; height: 16px;"></i>
                        <span>New Product</span>
                    </a>
                @endif

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-icon" title="Sign Out" style="color: var(--text-secondary);">
                        <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page View Body -->
        <div class="page-content">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="alert-toast alert-success">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer;">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-toast alert-error">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="shield-alert" style="width: 18px; height: 18px;"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer;">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-toast alert-error">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                            <strong>Please correct the following errors:</strong>
                        </div>
                        <ul style="margin-left: 24px; font-size: 0.85rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer;">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Global Quick Stock Action Modal (Only for authorized roles) -->
    @if(auth()->check() && auth()->user()->canRecordMovements())
        <div id="stockMovementModal" class="modal-backdrop">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title" style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="arrow-left-right" style="color: var(--primary);"></i>
                        <span>Record Stock Movement</span>
                    </h3>
                    <button type="button" class="btn-icon" onclick="closeModal('stockMovementModal')">&times;</button>
                </div>
                
                <form action="{{ route('stock.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Select Product *</label>
                            <select name="product_id" id="modal_product_id" class="form-control" required>
                                <option value="">-- Choose Item from Catalog --</option>
                                @php
                                    $allProducts = \App\Models\Product::orderBy('name')->get();
                                @endphp
                                @foreach($allProducts as $p)
                                    <option value="{{ $p->id }}">{{ $p->sku }} – {{ $p->name }} (Stock: {{ $p->current_stock }} {{ $p->unit }})</option>
                                @endforeach
                            </select>
                            <span id="modal_current_stock_display" style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;"></span>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Movement Type *</label>
                                <select name="type" id="modal_type" class="form-control" onchange="handleTypeChange(this.value)" required>
                                    <option value="IN">Stock IN (Receiving / Purchase)</option>
                                    <option value="OUT">Stock OUT (Sales / Dispatch)</option>
                                    @if(auth()->user()->canAdjustStock())
                                        <option value="ADJUSTMENT">Stock ADJUSTMENT (Physical Count Audit)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" id="modal_qty_label">Quantity to Receive (+)</label>
                                <input type="number" name="quantity" id="modal_quantity" class="form-control" min="1" required placeholder="e.g. 10">
                                <span id="modal_qty_help" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Adds to current stock.</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Reference / PO / Invoice Number</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. PO-8041, INV-2031, AUDIT-01">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Reason / Transaction Notes</label>
                            <textarea name="reason" rows="2" class="form-control" placeholder="e.g. Supplier delivery from Apex Tech, damaged box replacement..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('stockMovementModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            <span>Apply Movement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
