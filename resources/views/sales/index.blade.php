@extends('layouts.dashboard')

@section('title', 'Sales Orders')

@section('content')
<!-- Header Section -->
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Sales / Order Management</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">Sales Orders</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2">Track, manage and process customer sales orders.</p>
    </div>
    <div class="col-12 col-md-6 text-md-end d-flex justify-content-md-end gap-2">
        <a href="{{ route('sales.export', request()->query()) }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm bg-white">
            <i class="fas fa-file-csv me-2"></i>Export Report
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
            <i class="fas fa-plus me-2"></i>New Order
        </a>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="d-flex flex-wrap gap-2 mb-4">
    @foreach(['All' => '', 'Draft' => 'draft', 'Confirmed' => 'confirmed', 'Dispatched' => 'dispatched', 'Completed' => 'completed', 'Cancelled' => 'cancelled'] as $label => $val)
        <a href="{{ route('sales.index', $val ? ['status' => $val] : []) }}"
           class="btn rounded-pill px-3 py-1 small fw-bold {{ ($status ?? '') === $val ? 'btn-primary' : 'btn-outline-secondary border-light-subtle bg-white text-muted' }}"
           style="font-size: 13px;">
            {{ $label }}
        </a>
    @endforeach
</div>

<!-- Orders Table -->
<div class="data-card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0 text-muted fw-bold">Recent Orders</h3>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0 bg-light live-search-input" data-table="sales-table" placeholder="Search orders by number, customer...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="sales-table">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3 border-0">Order #</th>
                    <th class="py-3 border-0">Customer</th>
                    <th class="py-3 border-0">Items</th>
                    <th class="py-3 border-0">Total</th>
                    <th class="py-3 border-0 text-center">Status</th>
                    <th class="py-3 border-0">Date</th>
                    <th class="pe-4 py-3 border-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($orders as $order)
                <tr id="order-row-{{ $order->id }}">
                    <td class="ps-4 py-3">
                        <a href="{{ route('sales.show', encrypt($order->id)) }}" class="fw-bold text-primary text-decoration-none h6 mb-0">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="py-3">
                        <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                        <div class="extra-small text-muted">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="py-3 text-muted">{{ $order->items->count() }} item(s)</td>
                    <td class="py-3 fw-bold text-dark">₹{{ number_format($order->total_amount, 2) }}</td>
                    <td class="py-3 text-center">{!! $order->status_badge !!}</td>
                    <td class="py-3 text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="pe-4 py-3 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('sales.show', encrypt($order->id)) }}" class="btn btn-sm btn-info text-white px-3">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            @if($order->status === 'draft')
                            <button onclick="confirmOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-sm btn-success px-3">
                                <i class="fas fa-check me-1"></i> Confirm
                            </button>
                            <button onclick="cancelOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-sm btn-danger px-3">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            @endif
                            @if($order->status === 'confirmed')
                            <button onclick="dispatchOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-sm btn-warning text-white px-3">
                                <i class="fas fa-truck me-1"></i> Dispatch
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted border-0">
                        <i class="fas fa-file-invoice d-block mb-3 opacity-25 h1"></i>
                        No orders found. <a href="{{ route('sales.create') }}" class="text-primary fw-bold">Create one now →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="p-4 border-top">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<!-- Toast -->
<div id="toast" style="display:none; position:fixed; bottom:24px; right:24px; padding:14px 24px; border-radius:10px; font-size:14px; font-weight:600; color:white; z-index:9999; min-width:280px;"></div>
@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

function ajaxPost(url, successMsg, failCallback) {
    $.ajax({
        url: url,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function(res) {
            if (res.success) {
                showToast(res.message, '#16a34a');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(res.message, '#ef4444');
            }
        },
        error: function(xhr) {
            showToast(xhr.responseJSON?.message || 'An error occurred.', '#ef4444');
        }
    });
}

function confirmOrder(id, num) {
    if (!confirm(`Confirm order ${num}? Stock will be deducted.`)) return;
    ajaxPost(`/sales/${id}/confirm`);
}

function dispatchOrder(id, num) {
    if (!confirm(`Mark order ${num} as dispatched?`)) return;
    ajaxPost(`/sales/${id}/dispatch`);
}

function cancelOrder(id, num) {
    if (!confirm(`Cancel order ${num}? If confirmed, stock will be restored.`)) return;
    ajaxPost(`/sales/${id}/cancel`);
}

function showToast(msg, bg) {
    const t = $('#toast');
    t.text(msg).css('background', bg).fadeIn();
    setTimeout(() => t.fadeOut(), 4000);
}
</script>
@endsection
