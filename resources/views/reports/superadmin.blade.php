@extends('layouts.dashboard')

@section('title', 'Superadmin Analytics Report')

@section('content')
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Analytics / Executive Report</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title"><i class="fas fa-chart-pie text-primary me-2"></i> Superadmin Project Report</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2">High-level graphical overview of the entire business operation.</p>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <button onclick="window.print()" class="btn btn-dark px-4 py-2 shadow-sm">
            <i class="fas fa-print me-2"></i> Print Report
        </button>
    </div>
</div>

<style>
    @media print {
        nav, .sidebar, .top-header, button { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .data-card { box-shadow: none !important; border: 1px solid #ddd !important; }
        canvas { max-height: 300px !important; }
    }
</style>

<!-- High-level Stats -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="data-card border-0 shadow-sm text-center py-5 h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;">
            <i class="fas fa-boxes fa-3x mb-3 opacity-75"></i>
            <div class="extra-small text-uppercase fw-bold mb-1" style="letter-spacing: 1px; color: #bfdbfe;">Total Warehouse Valuation</div>
            <h2 class="display-5 fw-bold mb-0">₹{{ number_format($totalValuation) }}</h2>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="data-card border-0 shadow-sm text-center py-5 h-100" style="background: linear-gradient(135deg, #064e3b 0%, #10b981 100%); color: white;">
            <i class="fas fa-chart-line fa-3x mb-3 opacity-75"></i>
            <div class="extra-small text-uppercase fw-bold mb-1" style="letter-spacing: 1px; color: #a7f3d0;">All-Time Gross Revenue</div>
            <h2 class="display-5 fw-bold mb-0">₹{{ number_format($allTimeRevenue) }}</h2>
        </div>
    </div>
</div>

<!-- Graphs Row -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="data-card border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h6 fw-bold text-dark mb-0 text-uppercase letter-spacing-1">12-Month Revenue & Order Trend</h3>
                <span class="badge bg-light text-dark border">Performance</span>
            </div>
            <div style="height: 350px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-xl-4">
        <div class="data-card border-0 shadow-sm h-100">
            <h3 class="h6 fw-bold text-dark mb-4 text-uppercase letter-spacing-1">Stock Distribution by Category</h3>
            <div style="height: 280px;">
                <canvas id="categoryStockChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- User Performance Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="data-card border-0 shadow-sm">
            <h3 class="h6 fw-bold text-dark mb-4 text-uppercase letter-spacing-1">User Performance (Orders Created)</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="border-0 py-3 ps-3">User Name</th>
                            <th class="border-0 py-3 text-center">Orders Handled</th>
                            <th class="border-0 py-3 text-end">Value Generated</th>
                            <th class="border-0 py-3 pe-3 text-end">Contribution</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @php $maxOrders = $userPerformance->max('sale_orders_count') ?: 1; @endphp
                        @forelse($userPerformance as $user)
                        <tr>
                            <td class="ps-3 py-3 fw-bold text-dark">
                                <i class="fas fa-user-circle text-primary me-2"></i>{{ $user->name }}
                            </td>
                            <td class="py-3 text-center fw-bold">{{ $user->sale_orders_count }}</td>
                            <td class="py-3 text-end fw-bold text-success">₹{{ number_format($user->sale_orders_sum_total_amount) }}</td>
                            <td class="pe-3 py-3 text-end" style="width: 200px;">
                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                    <div class="progress-bar bg-primary" style="width: {{ ($user->sale_orders_count / $maxOrders) * 100 }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No user activity recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 12-Month Trend Chart (Mixed Chart: Bar for Orders, Line for Revenue)
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [
                {
                    type: 'line',
                    label: 'Revenue (₹)',
                    data: {!! json_encode($trendRevenue) !!},
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    borderWidth: 2,
                    yAxisID: 'y1',
                    tension: 0.3
                },
                {
                    type: 'bar',
                    label: 'Orders Count',
                    data: {!! json_encode($trendOrders) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Number of Orders' },
                    grid: { display: false }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Revenue (₹)' },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Category Stock Distribution (Pie Chart)
    const ctxCategory = document.getElementById('categoryStockChart').getContext('2d');
    const categoryData = {!! json_encode($categoryStock) !!};
    new Chart(ctxCategory, {
        type: 'pie',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                data: categoryData.map(c => c.stock),
                backgroundColor: ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ec4899', '#f43f5e'],
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 11 } } }
            }
        }
    });
});
</script>
@endsection
