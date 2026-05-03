@extends('layouts.dashboard')

@section('title', 'Order - ' . $order->order_number)

@section('content')
<style>
    @media print {
        /* Hide browser-added header/footer */
        @page {
            size: auto;
            margin: 0mm !important;
        }
        
        /* Hide ALL dashboard elements */
        nav, .sidebar, .header, .top-bar, .sidebar-overlay, .mobile-toggle,
        .action-buttons, .back-btn, button, .no-print, header, footer {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Reset main layout radically */
        body, .main-content, .main-wrapper, .content-wrapper, #app {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-width: 100% !important;
            position: static !important;
            display: block !important;
            left: 0 !important;
            top: 0 !important;
        }

        #printable-invoice {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 10mm !important; 
            border: none !important;
            box-shadow: none !important;
            position: static !important;
            background: white !important;
            visibility: visible !important;
            box-sizing: border-box !important;
            transform: scale(0.98); /* Slight scale down to prevent any cutoff */
            transform-origin: top left;
        }

        /* Ensure images and colors print correctly */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    }
</style>
<!-- Top Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 no-print">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('sales.index') }}" class="text-muted back-btn-minimal me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-1 fw-bold text-dark">{{ $order->order_number }}</h1>
            <div class="d-flex align-items-center gap-2">
                {!! $order->status_badge !!}
                <span class="small text-muted">Created {{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        @if($order->status === 'draft')
        <button onclick="confirmOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-primary flex-fill flex-md-grow-0">
            <i class="fas fa-check me-2"></i>Confirm
        </button>
        <button onclick="cancelOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-danger-light flex-fill flex-md-grow-0">
            <i class="fas fa-times me-2"></i>Cancel
        </button>
        @endif
        
        @if($order->status === 'confirmed')
        <button onclick="dispatchOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-warning text-white flex-fill flex-md-grow-0">
            <i class="fas fa-truck me-2"></i>Dispatch
        </button>
        <button onclick="cancelOrder('{{ encrypt($order->id) }}', '{{ $order->order_number }}')" class="btn btn-danger-light flex-fill flex-md-grow-0">
            <i class="fas fa-undo me-2"></i>Cancel & Restore
        </button>
        @endif
        
        <a href="{{ route('sales.print', encrypt($order->id)) }}" target="_blank" class="btn btn-light border flex-fill flex-md-grow-0">
            <i class="fas fa-print me-2"></i>Print
        </a>
    </div>
</div>

<!-- Invoice Card -->
<div class="card border-0 shadow-sm p-3 p-md-5" id="printable-invoice" style="position: relative; overflow: hidden; border: 1px solid rgba(226, 232, 240, 0.1); border-radius: 20px;">
    
    <!-- Watermark Background (Optimized for Web & Print) -->
    <div class="watermark-text" style="position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 130px; font-weight: 900; color: rgba(100, 116, 139, 0.07); pointer-events: none; white-space: nowrap; text-transform: uppercase; z-index: 0; -webkit-print-color-adjust: exact !important;">
        SUNUltra 5G
    </div>

    <!-- Header Row -->
    <div class="row align-items-start mb-4 pb-4 border-bottom border-primary border-2">
        <div class="col-12 col-md-6 mb-3 mb-md-0 text-center text-md-start">
            <img src="{{ asset('assets/images/logo-white.svg') }}" alt="SUNUltra 5G" class="img-fluid" style="max-height: 55px;">
        </div>
        <div class="col-12 col-md-6 text-center text-md-end">
            <div class="small fw-bold text-primary text-uppercase mb-1" style="letter-spacing: 2px;">Tax Invoice</div>
            <div class="h3 fw-bold text-dark mb-1">{{ $order->order_number }}</div>
            <div class="text-muted small">Date: <span class="fw-bold text-dark">{{ $order->created_at->format('d M Y') }}</span></div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-7">
            <div class="p-4 rounded-3 h-100 border border-dashed border-secondary border-opacity-25">
                <div class="small fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px;">Billed To</div>
                <div class="h4 fw-bold text-dark-mode-white mb-2">{{ $order->customer_name }}</div>
                @if($order->customer_phone)
                <div class="small text-secondary mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-phone-alt text-primary"></i> {{ $order->customer_phone }}
                </div>
                @endif
                @if($order->customer_address)
                <div class="small text-secondary d-flex align-items-start gap-2">
                    <i class="fas fa-map-marker-alt text-primary mt-1"></i> {{ $order->customer_address }}
                </div>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-5">
            <div class="p-4 rounded-3 h-100 border border-dashed text-secondary">
                <div class="small fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px;">Order Summary</div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small">Status:</span>
                    <span class="small fw-bold text-dark text-uppercase">{!! strip_tags($order->status_badge) !!}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small">Total Items:</span>
                    <span class="small fw-bold text-dark">{{ $order->items->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small">Payment Mode:</span>
                    <span class="small fw-bold text-dark">{{ $order->payment_mode ?? 'Cash' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive rounded-3 border mb-5">
        <table class="table mb-0">
            <thead>
                <tr class="bg-primary-dark text-white">
                    <th class="ps-3 py-3 small text-uppercase fw-bold">#</th>
                    <th class="py-3 small text-uppercase fw-bold">Description</th>
                    <th class="py-3 text-center small text-uppercase fw-bold">Unit</th>
                    <th class="py-3 text-center small text-uppercase fw-bold">Qty</th>
                    <th class="py-3 text-end small text-uppercase fw-bold">Price</th>
                    <th class="pe-3 py-3 text-end small text-uppercase fw-bold">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                <tr class="border-bottom">
                    <td class="ps-3 py-3 text-muted small">{{ sprintf('%02d', $i + 1) }}</td>
                    <td class="py-3">
                        <div class="fw-bold text-dark">{{ $item->inventory->name }}</div>
                        <div class="small text-muted mt-1">Category: {{ $item->inventory->category->name ?? 'General' }}</div>
                    </td>
                    <td class="py-3 text-center text-secondary small">{{ $item->inventory->unit }}</td>
                    <td class="py-3 text-center fw-bold text-dark">{{ $item->quantity }}</td>
                    <td class="py-3 text-end text-secondary small">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="pe-3 py-3 text-end fw-bold text-dark">₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bottom Section -->
    <div class="row g-4 align-items-end">
        <div class="col-12 col-md-7">
            @if($order->notes)
            <div class="p-3 mb-4 rounded-3 border border-warning-subtle" style="background: #fffbeb;">
                <div class="small fw-bold text-warning-emphasis text-uppercase mb-2">Instructions:</div>
                <div class="small text-secondary font-italic">"{{ $order->notes }}"</div>
            </div>
            @endif
            <div class="small text-muted" style="line-height: 1.6;">
                <strong class="text-dark">Terms & Conditions:</strong><br>
                1. Goods once sold will not be taken back.<br>
                2. Subject to Chhattisgarh Jurisdiction only.
            </div>
        </div>
        <div class="col-12 col-md-5">
            <div class="p-4 rounded-3 border border-secondary border-opacity-25">
                <div class="d-flex justify-content-between pb-2 mb-2 border-bottom text-muted">
                    <span>Subtotal</span>
                    <span class="fw-bold">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="h6 mb-0 fw-bold">Grand Total</span>
                    <span class="h4 mb-0 fw-bold text-primary">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="text-end small text-muted">Inclusive of all taxes</div>
            </div>
            
            <div class="mt-5 text-center">
                <div class="mb-2" style="height: 60px; border-bottom: 1px solid #e2e8f0; opacity: 0.1;"></div>
                <div class="small fw-bold text-dark">Authorized Signatory</div>
                <div class="small text-muted mt-1">For SUNUltra 5G</div>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" style="display:none; position:fixed; bottom:24px; right:24px; padding:14px 24px; border-radius:10px; font-size:14px; font-weight:600; color:white; z-index:9999; min-width:280px;"></div>
@endsection

@section('scripts')
<script>
function ajaxPost(url) {
    $.ajax({
        url: url,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            showToast(res.message, res.success ? '#16a34a' : '#ef4444');
            if (res.success) setTimeout(() => location.reload(), 1200);
        },
        error: function(xhr) {
            showToast(xhr.responseJSON?.message || 'Error occurred.', '#ef4444');
        }
    });
}

function confirmOrder(id, num) {
    if (!confirm(`Confirm order ${num}? Stock will be deducted immediately.`)) return;
    ajaxPost(`/sales/${id}/confirm`);
}

function dispatchOrder(id, num) {
    if (!confirm(`Mark order ${num} as dispatched?`)) return;
    ajaxPost(`/sales/${id}/dispatch`);
}

function cancelOrder(id, num) {
    if (!confirm(`Cancel order ${num}? Stock will be restored if order was confirmed.`)) return;
    ajaxPost(`/sales/${id}/cancel`);
}

function showToast(msg, bg) {
    const t = $('#toast');
    t.text(msg).css('background', bg).fadeIn();
    setTimeout(() => t.fadeOut(), 4000);
}
</script>
@endsection
