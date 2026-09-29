@extends('layouts.app')

@section('title', 'Analytics & Inventory Dashboard')
@section('page_title', 'Real-Time Inventory Overview')

@section('content')
    <!-- KPI Summary Grid -->
    <div class="kpi-grid">
        <!-- Total SKUs -->
        <div class="kpi-card kpi-indigo">
            <div class="kpi-header">
                <span class="kpi-label">Active Catalog</span>
                <div class="kpi-icon">
                    <i data-lucide="package"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalProducts }}</div>
            <div class="kpi-sub">Total distinct SKUs tracked</div>
        </div>

        <!-- Total Items In Stock -->
        <div class="kpi-card kpi-sky">
            <div class="kpi-header">
                <span class="kpi-label">Physical Units</span>
                <div class="kpi-icon">
                    <i data-lucide="layers"></i>
                </div>
            </div>
            <div class="kpi-value">{{ number_format($totalItemsInStock) }}</div>
            <div class="kpi-sub">Total units currently in stock</div>
        </div>

        <!-- Inventory Cost Valuation -->
        <div class="kpi-card kpi-emerald">
            <div class="kpi-header">
                <span class="kpi-label">Stock Valuation (Cost)</span>
                <div class="kpi-icon">
                    <i data-lucide="indian-rupee"></i>
                </div>
            </div>
            <div class="kpi-value" title="₹{{ number_format($totalCostValuation, 2) }}">₹{{ number_format($totalCostValuation, 2) }}</div>
            <div class="kpi-sub" title="₹{{ number_format($totalRetailValuation, 2) }}">Retail value: ₹{{ number_format($totalRetailValuation, 2) }}</div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="kpi-card {{ $lowStockCount > 0 || $outOfStockCount > 0 ? 'kpi-rose' : 'kpi-amber' }}">
            <div class="kpi-header">
                <span class="kpi-label">Inventory Attention</span>
                <div class="kpi-icon">
                    <i data-lucide="alert-octagon"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $lowStockCount + $outOfStockCount }}</div>
            <div class="kpi-sub">
                <span style="color: var(--danger);">{{ $outOfStockCount }} Out of stock</span> &bull; 
                <span style="color: var(--warning);">{{ $lowStockCount }} Low stock</span>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- 7-Day In/Out Movement Trend -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i data-lucide="bar-chart-3" style="color: var(--primary);"></i>
                    <span>7-Day Stock Inflow vs Outflow</span>
                </div>
            </div>
            <div class="panel-body">
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="movementTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i data-lucide="pie-chart" style="color: var(--info);"></i>
                    <span>Category Valuation</span>
                </div>
            </div>
            <div class="panel-body">
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="categoryDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Two-Column Operational Lists -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px;">
        <!-- Urgent Restock Alert List -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title" style="color: var(--warning);">
                    <i data-lucide="alert-triangle"></i>
                    <span>Low Stock Alert (Restock Needed)</span>
                </div>
                <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>SKU & Product</th>
                            <th>Current</th>
                            <th>Min Alert</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $lp)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $lp->name }}</div>
                                    <span class="sku-badge">{{ $lp->sku }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 700; font-size: 1rem; color: {{ $lp->current_stock <= 0 ? 'var(--danger)' : 'var(--warning)' }};">
                                        {{ $lp->current_stock }} {{ $lp->unit }}
                                    </span>
                                </td>
                                <td>{{ $lp->min_stock_alert }} {{ $lp->unit }}</td>
                                <td>
                                    <span class="badge {{ $lp->stock_status_badge_class }}">
                                        <span class="badge-dot"></span>
                                        {{ $lp->stock_status_label }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openQuickStockModal('{{ $lp->id }}', '{{ addslashes($lp->name) }}', {{ $lp->current_stock }}, 'IN')">
                                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                        <span>Restock</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    All inventory levels are currently healthy! No low stock alerts.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i data-lucide="history" style="color: var(--primary);"></i>
                    <span>Recent Stock Movements</span>
                </div>
                <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Full Ledger</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Change</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMovements as $rm)
                            <tr>
                                <td style="color: var(--text-muted); font-size: 0.8rem;">
                                    {{ $rm->created_at->diffForHumans() }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem;">{{ $rm->product?->name ?? 'Deleted Item' }}</div>
                                    <span class="sku-badge">{{ $rm->product?->sku ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($rm->type === 'IN')
                                        <span class="badge badge-success"><span class="badge-dot"></span> IN</span>
                                    @elseif($rm->type === 'OUT')
                                        <span class="badge badge-info"><span class="badge-dot"></span> OUT</span>
                                    @else
                                        <span class="badge badge-warning"><span class="badge-dot"></span> AUDIT</span>
                                    @endif
                                </td>
                                <td style="font-weight: 700;">
                                    {{ $rm->type === 'IN' ? '+' : ($rm->type === 'OUT' ? '-' : '') }}{{ $rm->quantity }}
                                </td>
                                <td style="color: var(--text-secondary);">
                                    {{ $rm->resulting_stock }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    No stock movements recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // 7-Day Trend Chart
    const trendCtx = document.getElementById('movementTrendsChart').getContext('2d');
    const trendData = @json($movementTrends);

    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [
                {
                    label: 'Stock In (Received)',
                    data: trendData.map(d => d.in),
                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                },
                {
                    label: 'Stock Out (Dispatched)',
                    data: trendData.map(d => d.out),
                    backgroundColor: 'rgba(99, 102, 241, 0.75)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 12 } }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', stepSize: 5 }
                }
            }
        }
    });

    // Category Doughnut Chart
    const catCtx = document.getElementById('categoryDoughnutChart').getContext('2d');
    const catData = @json($categoryBreakdown);

    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.name),
            datasets: [{
                data: catData.map(c => c.stock_value),
                backgroundColor: [
                    '#6366f1',
                    '#10b981',
                    '#f59e0b',
                    '#0ea5e9',
                    '#ec4899',
                    '#8b5cf6'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8', boxWidth: 12, font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ₹${context.raw.toLocaleString('en-IN')}`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>
@endsection
