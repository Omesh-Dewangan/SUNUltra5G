@extends('layouts.dashboard')

@section('title', 'Administrative Dashboard')

@section('content')
<style>
    @media (max-width: 768px) {
        .stat-card { padding: 15px !important; }
        .stat-value { font-size: 20px !important; }
        .stat-label { font-size: 11px !important; }
        .content-header h1 { font-size: 20px !important; }
    }
</style>
<div class="container-fluid p-0">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div class="w-100">
            <h1 class="h3 fw-bold text-dark mb-1">System Overview</h1>
            <p class="text-muted small mb-0">Real-time analytics and inventory control panel.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
            @if(Auth::user()->hasRole('super_admin'))
            <a href="{{ route('rbac.superadmin_report') }}" class="btn btn-dark shadow-sm small px-3 fw-bold flex-fill flex-md-grow-0">
                <i class="fas fa-chart-pie me-2"></i>Project Report
            </a>
            @endif
            <button class="btn btn-white shadow-sm border text-muted small flex-fill flex-md-grow-0"><i class="fas fa-download me-2"></i>Export Report</button>
            <a href="{{ route('sales.index') }}" class="btn btn-primary shadow-sm small px-4 fw-bold flex-fill flex-md-grow-0">New Sales Order</a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="stat-label">Monthly LED Sales</span>
                        <span class="stat-value text-primary">₹{{ number_format($totalLedSales / 1000, 1) }}k</span>
                    </div>
                    <div class="bg-primary-light p-2 rounded-3 text-primary">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>{{ $ledTrend }}%</span>
                    <span class="text-muted ms-1">vs last month</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="stat-label">Total Wire Stock</span>
                        <span class="stat-value text-success">{{ number_format($wireStock) }}m</span>
                    </div>
                    <div class="bg-success-light p-2 rounded-3 text-success">
                        <i class="fas fa-bolt"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    <span class="badge {{ $wireStatus == 'Healthy' ? 'bg-success-light text-success' : 'bg-warning-light text-warning' }}">{{ $wireStatus }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="stat-label">Total Orders</span>
                        <span class="stat-value text-warning">{{ $totalOrdersCount }}</span>
                    </div>
                    <div class="bg-warning-light p-2 rounded-3 text-warning">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">Last 30 days activity</div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="stat-label">Active Dealers</span>
                        <span class="stat-value text-info">{{ $activeDealersCount }}</span>
                    </div>
                    <div class="bg-info-light p-2 rounded-3 text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <span class="fw-bold text-dark">{{ $dealerStatus }}</span> verification pending
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Analytics Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue Trend -->
        <div class="col-12 col-xl-8">
            <div class="data-card border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h6 fw-bold text-dark mb-0 text-uppercase letter-spacing-1">Revenue Trend (Last 6 Months)</h3>
                    <span class="badge bg-primary-light text-primary px-3">Growth Analysis</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="col-12 col-xl-4">
            <div class="data-card border-0 shadow-sm h-100">
                <h3 class="h6 fw-bold text-dark mb-4 text-uppercase letter-spacing-1">Category Revenue Mix</h3>
                <div style="height: 250px;">
                    <canvas id="categoryRevenueChart"></canvas>
                </div>
                <div class="mt-4">
                    <div id="categoryLegend" class="d-flex flex-wrap gap-2 justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Top Selling Products -->
        <div class="col-12 col-lg-7">
            <div class="data-card border-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h6 fw-bold text-dark mb-0 text-uppercase letter-spacing-1">Top Selling Products</h3>
                    <small class="text-muted">Last 30 Days</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 extra-small fw-bold text-uppercase py-3">Product</th>
                                <th class="border-0 extra-small fw-bold text-uppercase py-3 text-center">Units Sold</th>
                                <th class="border-0 extra-small fw-bold text-uppercase py-3 text-end">Popularity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSellingProducts as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold small text-dark">{{ $item->inventory->name }}</div>
                                    <div class="extra-small text-muted">{{ $item->inventory->code }}</div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-success-light text-success rounded-pill px-3">{{ number_format($item->total_sold) }} {{ $item->inventory->unit }}</span>
                                </td>
                                <td class="py-3 text-end" style="width: 120px;">
                                    <div class="progress" style="height: 6px; border-radius: 10px;">
                                        @php $perc = min(100, ($item->total_sold / ($topSellingProducts->first()->total_sold ?? 1)) * 100); @endphp
                                        <div class="progress-bar bg-primary" style="width: {{ $perc }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted small">No sales data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Critical Re-order List -->
        <div class="col-12 col-lg-5">
            <div class="data-card border-0 shadow-sm h-100 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="h6 fw-bold text-danger mb-1 text-uppercase letter-spacing-1">Critical Re-order List</h3>
                        <p class="extra-small text-muted mb-0">Below minimum safety threshold</p>
                    </div>
                    <span class="badge bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">{{ $lowStockCount }}</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($lowStockItems as $item)
                    <div class="p-3 rounded-4 border bg-light-subtle position-relative overflow-hidden">
                        @if($item->stock_quantity == 0)
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="pulsate-red d-block rounded-circle" style="width: 8px; height: 8px;"></span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="min-w-0">
                                <div class="fw-bold small text-dark text-truncate">{{ $item->name }}</div>
                                <div class="extra-small text-muted">SKU: {{ $item->code }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $item->stock_quantity == 0 ? 'text-danger' : 'text-warning' }} small">{{ $item->stock_quantity }} {{ $item->unit }}</div>
                                <div class="extra-small text-muted">In Stock</div>
                            </div>
                        </div>
                        <div class="progress" style="height: 4px; border-radius: 10px; background: #eee;">
                            @php 
                                $perc = ($item->stock_quantity / ($item->low_stock_threshold ?: 1)) * 100;
                                $color = $item->stock_quantity == 0 ? 'bg-danger' : 'bg-warning';
                            @endphp
                            <div class="progress-bar {{ $color }}" style="width: {{ min(100, $perc) }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="extra-small text-muted">Threshold: {{ $item->low_stock_threshold }}</span>
                            <a href="{{ route('inventory.index') }}" class="extra-small fw-bold text-primary text-decoration-none">Order Stock <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fs-1 mb-3 opacity-25"></i>
                        <p class="text-muted small">All stock levels are healthy!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Movement Trend & Recent -->
    <div class="row g-4">
        <!-- Stock Trend -->
        <div class="col-12 col-xl-8">
            <div class="data-card border-0 shadow-sm h-100">
                <h3 class="h6 fw-bold text-dark mb-4 text-uppercase letter-spacing-1">Inventory Movement Trend (Last 7 Days)</h3>
                <div style="height: 350px;">
                    <canvas id="stockTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-12 col-xl-4">
            <div class="data-card border-0 shadow-sm h-100">
                <h3 class="h6 fw-bold text-dark mb-4 text-uppercase letter-spacing-1">Recent Activity</h3>
                <div class="activity-timeline">
                    @foreach($recentTransactions as $tx)
                    <div class="d-flex gap-3 mb-4 position-relative">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center {{ $tx->type == 'in' ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}" style="width: 32px; height: 32px;">
                                <i class="fas fa-{{ $tx->type == 'in' ? 'arrow-down' : 'arrow-up' }} small"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small fw-bold text-dark">{{ $tx->inventory->name }}</div>
                            <div class="extra-small text-muted">{{ $tx->type == 'in' ? 'Added' : 'Removed' }} {{ $tx->quantity }} {{ $tx->inventory->unit }} • {{ $tx->created_at->diffForHumans() }}</div>
                            @if($tx->remarks)
                            <div class="extra-small mt-1 px-2 py-1 bg-light rounded border text-muted">"{{ $tx->remarks }}"</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('inventory.index') }}" class="btn btn-light w-100 extra-small fw-bold py-2 mt-auto">View All Movement</a>
            </div>
        </div>
    </div>

    <!-- Sales Orders Overview - News Feed Style -->
    <div class="row g-4 mt-0 mb-4">
        <div class="col-12">
            <div class="data-card border-0 shadow-sm">
                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                    <div>
                        <h3 class="h6 fw-bold text-dark mb-1 text-uppercase letter-spacing-1">
                            <i class="fas fa-receipt text-primary me-2"></i>Sales Orders Live Feed
                        </h3>
                        <p class="extra-small text-muted mb-0">Real-time order status across all stages</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm px-4 fw-bold flex-shrink-0">
                        <i class="fas fa-plus me-1"></i> New Order
                    </a>
                </div>

                <!-- Status Count Pills -->
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="{{ route('sales.index') }}" class="text-decoration-none">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#f1f5f9; color:#64748b; font-size:12px;">
                            All &nbsp;<strong>{{ array_sum($orderStatusCounts) }}</strong>
                        </span>
                    </a>
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#fef9c3; color:#a16207; font-size:12px;">
                        <i class="fas fa-pencil-alt me-1" style="font-size:9px;"></i>Draft &nbsp;<strong>{{ $orderStatusCounts['draft'] }}</strong>
                    </span>
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#dbeafe; color:#1d4ed8; font-size:12px;">
                        <i class="fas fa-check me-1" style="font-size:9px;"></i>Confirmed &nbsp;<strong>{{ $orderStatusCounts['confirmed'] }}</strong>
                    </span>
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#dcfce7; color:#16a34a; font-size:12px;">
                        <i class="fas fa-truck me-1" style="font-size:9px;"></i>Dispatched &nbsp;<strong>{{ $orderStatusCounts['dispatched'] }}</strong>
                    </span>
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#f3e8ff; color:#7c3aed; font-size:12px;">
                        <i class="fas fa-star me-1" style="font-size:9px;"></i>Completed &nbsp;<strong>{{ $orderStatusCounts['completed'] }}</strong>
                    </span>
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#fee2e2; color:#dc2626; font-size:12px;">
                        <i class="fas fa-times me-1" style="font-size:9px;"></i>Cancelled &nbsp;<strong>{{ $orderStatusCounts['cancelled'] }}</strong>
                    </span>
                </div>

                <!-- Recent Orders Feed -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="extra-small text-muted text-uppercase">
                                <th class="border-0 py-3 ps-3">Order #</th>
                                <th class="border-0 py-3">Customer</th>
                                <th class="border-0 py-3 text-center">Items</th>
                                <th class="border-0 py-3 text-end">Total</th>
                                <th class="border-0 py-3 text-center">Status</th>
                                <th class="border-0 py-3 text-end pe-3">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            @php
                                $statusConfig = [
                                    'draft'      => ['bg'=>'#fef9c3','color'=>'#a16207','icon'=>'pencil-alt','label'=>'Draft'],
                                    'confirmed'  => ['bg'=>'#dbeafe','color'=>'#1d4ed8','icon'=>'check','label'=>'Confirmed'],
                                    'dispatched' => ['bg'=>'#dcfce7','color'=>'#16a34a','icon'=>'truck','label'=>'Dispatched'],
                                    'completed'  => ['bg'=>'#f3e8ff','color'=>'#7c3aed','icon'=>'star','label'=>'Completed'],
                                    'cancelled'  => ['bg'=>'#fee2e2','color'=>'#dc2626','icon'=>'times','label'=>'Cancelled'],
                                ];
                                $cfg = $statusConfig[$order->status] ?? ['bg'=>'#f1f5f9','color'=>'#64748b','icon'=>'circle','label'=>ucfirst($order->status)];
                            @endphp
                            <tr>
                                <td class="ps-3 py-3">
                                    <a href="{{ route('sales.show', encrypt($order->id)) }}" class="fw-bold small text-primary text-decoration-none">
                                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold small text-dark">{{ $order->customer_name }}</div>
                                    <div class="extra-small text-muted">{{ $order->customer_phone ?? '—' }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-light text-dark border small">{{ $order->items_count ?? $order->items()->count() }} items</span>
                                </td>
                                <td class="py-3 text-end fw-bold small text-dark">
                                    ₹{{ number_format($order->total_amount, 0) }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background:{{ $cfg['bg'] }}; color:{{ $cfg['color'] }}; font-size:11px;">
                                        <i class="fas fa-{{ $cfg['icon'] }} me-1" style="font-size:9px;"></i>{{ $cfg['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 text-end pe-3 extra-small text-muted">
                                    {{ $order->created_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-receipt text-muted fs-2 mb-2 d-block opacity-25"></i>
                                    <p class="text-muted small mb-1">No orders found.</p>
                                    <a href="{{ route('sales.create') }}" class="small text-primary fw-bold text-decoration-none">Create first order →</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($recentOrders->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-light btn-sm px-4 extra-small fw-bold">View All Orders <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
$(document).ready(function() {
    // Stock Trend Chart
    const ctxTrend = document.getElementById('stockTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Stock In',
                    data: {!! json_encode($chartIn) !!},
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Stock Out',
                    data: {!! json_encode($chartOut) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { usePointStyle: true, font: { size: 11 } } } },
            scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
        }
    });

    // Revenue Trend Chart
    const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRevenueLabels) !!},
            datasets: [{
                label: 'Revenue (₹)',
                data: {!! json_encode($monthlyRevenueData) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
        }
    });

    // Category Breakdown Chart
    const ctxCategory = document.getElementById('categoryRevenueChart').getContext('2d');
    const categoryData = {!! json_encode($categoryRevenue) !!};
    const categoryChart = new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                data: categoryData.map(c => c.revenue),
                backgroundColor: ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });

    // Custom Legend for Category Chart
    const legendContainer = document.getElementById('categoryLegend');
    if (legendContainer) {
        categoryData.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'extra-small d-flex align-items-center gap-1 px-2 py-1 rounded bg-light border';
            div.innerHTML = `<span style="width: 8px; height: 8px; background: ${categoryChart.data.datasets[0].backgroundColor[index]}; border-radius: 50%;"></span> ${item.name}`;
            legendContainer.appendChild(div);
        });
    }
});
</script>
@endsection
