@extends('layouts.dashboard')

@section('title', 'Manage Stock - ' . $product->name)

@section('content')
<!-- Bootstrap Datepicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
<style>
    /* Datepicker wrapper - scopes the calendar so it positions correctly */
    #date-picker-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex-grow: 1;
    }
    /* Full width on mobile */
    @media (max-width: 576px) {
        #date-picker-wrapper {
            width: 100%;
        }
        #date-filter {
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    /* Inline clear button inside date input */
    .date-input-wrapper {
        position: relative;
        flex: 1;
    }
    .date-input-wrapper #date-filter {
        padding-right: 26px;
    }
    #date-clear-inline {
        position: absolute;
        right: 7px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        font-size: 11px;
        display: none;
        z-index: 10;
        line-height: 1;
    }
    #date-clear-inline:hover {
        color: #ef4444;
    }
    /* Override ONLY the global dashboard fixed positioning - let datepicker JS handle top/left */
    .datepicker.dropdown-menu {
        position: absolute !important;
        z-index: 9999 !important;
    }
    
    /* Dark Theme Support for Bootstrap Datepicker */
    [data-theme="dark"] .datepicker.dropdown-menu {
        background-color: #1e293b !important;
        border: 1px solid #334155 !important;
    }
    [data-theme="dark"] .datepicker table tr td, 
    [data-theme="dark"] .datepicker table tr th {
        color: #f1f5f9 !important;
    }
    [data-theme="dark"] .datepicker table tr td.day:hover,
    [data-theme="dark"] .datepicker table tr td.focused {
        background: rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] .datepicker table tr td.old, 
    [data-theme="dark"] .datepicker table tr td.new {
        color: #64748b !important;
    }
    [data-theme="dark"] .datepicker table tr td.today {
        background-color: rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
    }
    [data-theme="dark"] .datepicker table tr td.active, 
    [data-theme="dark"] .datepicker table tr td.active:hover {
        background-color: #3b82f6 !important;
        color: white !important;
        text-shadow: none !important;
    }
    [data-theme="dark"] .datepicker .datepicker-switch:hover, 
    [data-theme="dark"] .datepicker .prev:hover, 
    [data-theme="dark"] .datepicker .next:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }
</style>
<div class="container-fluid p-0">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('master.products') }}" class="text-decoration-none">Products</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Stock Management</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold text-dark mb-3">{{ $product->name }}</h1>
                    <div class="d-flex flex-wrap gap-2 mb-0">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-barcode text-muted me-2"></i>SKU: {{ $product->code }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-tag text-muted me-2"></i>{{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        @if($product->wattage)
                        <span class="badge bg-warning-light text-dark border px-3 py-2">
                            <i class="fas fa-bolt text-warning me-1"></i>{{ $product->wattage }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-end mt-4 mt-md-0">
                    <div class="bg-light p-4 rounded-4 border">
                        <div class="small text-muted text-uppercase fw-bold mb-1 letter-spacing-1">Current Balance</div>
                        <div class="d-flex align-items-baseline justify-content-md-end gap-2">
                            <span class="display-6 fw-bold text-dark">{{ $product->stock_quantity }}</span>
                            <span class="fw-bold text-muted">{{ $product->unit }}</span>
                        </div>
                        @if($product->stock_quantity <= $product->low_stock_threshold)
                        <div class="mt-2">
                            <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> Low Stock</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-lift overflow-hidden" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-4">
                        <div class="bg-success-light text-success p-4 rounded-4 mx-auto mx-sm-0">
                            <i class="fas fa-plus-circle fs-3"></i>
                        </div>
                        <div class="text-center text-sm-start w-100">
                            <h3 class="h5 fw-bold text-dark mb-1">Stock Inbound</h3>
                            <p class="text-muted small mb-3">Add new stock received from suppliers or returns.</p>
                            <button class="btn btn-success btn-sm px-4 fw-bold w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#stockInModal">
                                <i class="fas fa-arrow-down me-2"></i>Process Stock In
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-lift overflow-hidden" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-4">
                        <div class="bg-danger-light text-danger p-4 rounded-4 mx-auto mx-sm-0">
                            <i class="fas fa-minus-circle fs-3"></i>
                        </div>
                        <div class="text-center text-sm-start w-100">
                            <h3 class="h5 fw-bold text-dark mb-1">Stock Outbound</h3>
                            <p class="text-muted small mb-3">Issue stock for sales, damage or internal use.</p>
                            <button class="btn btn-danger btn-sm px-4 fw-bold w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#stockOutModal">
                                <i class="fas fa-arrow-up me-2"></i>Process Stock Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-header bg-white py-4 px-4 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="h5 fw-bold text-dark mb-0">Transaction Audit Trail</h3>
                <div class="d-flex align-items-center flex-wrap gap-2 w-100 w-sm-auto justify-content-sm-end">
                    <div id="date-picker-wrapper" class="flex-grow-1 flex-sm-grow-0">
                        <div class="date-input-wrapper">
                            <input type="text" id="date-filter" class="form-control form-control-sm border text-muted shadow-sm w-100" style="min-width: 120px; max-width: 200px;" placeholder="Select Date" title="Filter by date">
                            <span id="date-clear-inline" title="Clear date"><i class="fas fa-times"></i></span>
                        </div>
                    </div>
                    <div class="btn-group shadow-sm rounded-3 overflow-hidden flex-grow-1 flex-sm-grow-0">
                        <button type="button" class="btn btn-light btn-sm filter-btn active fw-bold px-3 border" data-filter="all">
                            <i class="fas fa-list me-1"></i> All
                        </button>
                        <button type="button" class="btn btn-light btn-sm filter-btn px-3 border text-success" data-filter="in">
                            <i class="fas fa-arrow-down me-1"></i> In
                        </button>
                        <button type="button" class="btn btn-light btn-sm filter-btn px-3 border text-danger" data-filter="out">
                            <i class="fas fa-arrow-up me-1"></i> Out
                        </button>
                    </div>
                    <a id="export-csv-btn"
                       href="{{ route('master.products.stock.export', encrypt($product->id)) }}"
                       class="btn btn-sm px-3 fw-bold border shadow-sm"
                       style="background:rgba(34,197,94,0.1);color:#16a34a;border-color:rgba(34,197,94,0.3) !important;"
                       title="Export to CSV">
                        <i class="fas fa-file-csv me-1"></i> <span class="d-none d-sm-inline">Export</span> CSV
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase extra-small fw-bold text-muted letter-spacing-1 border-0">Date & Time</th>
                            <th class="py-3 text-uppercase extra-small fw-bold text-muted letter-spacing-1 border-0">Type</th>
                            <th class="py-3 text-uppercase extra-small fw-bold text-muted letter-spacing-1 border-0">Movement</th>
                            <th class="py-3 text-uppercase extra-small fw-bold text-muted letter-spacing-1 border-0">Valuation</th>
                            <th class="py-3 text-uppercase extra-small fw-bold text-muted letter-spacing-1 border-0">Reference / Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr class="transaction-row"
                            data-type="{{ $transaction->type }}"
                            data-date="{{ $transaction->created_at->format('Y-m-d') }}"
                            data-datetime="{{ $transaction->created_at->format('d M Y h:i A') }}"
                            data-qty="{{ ($transaction->type == 'in' ? '+' : '-') . number_format($transaction->quantity) }} {{ $product->unit }}"
                            data-price="{{ $transaction->price_per_unit ? number_format($transaction->price_per_unit, 2) : '-' }}"
                            data-remarks="{{ addslashes($transaction->remarks ?? 'No remarks provided') }}">
                            <td class="ps-4">
                                <div class="fw-bold text-dark small">{{ $transaction->created_at->format('d M Y') }}</div>
                                <div class="extra-small text-muted">{{ $transaction->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($transaction->type == 'in')
                                <span class="badge bg-success-light text-success border border-success border-opacity-10 px-3 rounded-pill">INBOUND</span>
                                @else
                                <span class="badge bg-danger-light text-danger border border-danger border-opacity-10 px-3 rounded-pill">OUTBOUND</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold {{ $transaction->type == 'in' ? 'text-success' : 'text-danger' }} fs-6">
                                        {{ $transaction->type == 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                    </span>
                                    <span class="extra-small text-muted text-uppercase">{{ $product->unit }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark fw-bold">
                                    {{ $transaction->price_per_unit ? '₹' . number_format($transaction->price_per_unit, 2) : '-' }}
                                </div>
                                <div class="extra-small text-muted">Per Unit</div>
                            </td>
                            <td>
                                <div class="small text-muted fst-italic">{{ $transaction->remarks ?? 'No remarks provided' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-25 mb-3"><i class="fas fa-history display-4"></i></div>
                                <p class="text-muted small">No movement history found for this product.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-footer bg-white border-0 py-4">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle text-success me-2"></i>Add Inventory Stock</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stock-in-form">
                @csrf
                <input type="hidden" name="type" value="in">
                <div class="modal-body p-4 pt-0">
                    <div class="alert alert-success border-0 rounded-4 d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle fs-4"></i>
                        <div class="small">Adding stock will increase the current balance of <strong>{{ $product->name }}</strong>.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Quantity to Add <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" class="form-control px-3 py-2 fs-5 fw-bold" required min="1" placeholder="0">
                            <span class="input-group-text bg-light text-muted fw-bold">{{ $product->unit }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Purchase/Valuation Rate (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">₹</span>
                            <input type="number" name="price_per_unit" step="0.01" class="form-control px-3 py-2" placeholder="0.00">
                        </div>
                        <div class="extra-small text-muted mt-1">Cost price per single {{ $product->unit }}</div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase">Remarks / Reference</label>
                        <textarea name="remarks" class="form-control px-3 py-2" rows="2" placeholder="e.g. GRN #123, From Supplier X..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="submit-in-btn" class="btn btn-success px-4 fw-bold shadow-sm">Confirm Inbound</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-minus-circle text-danger me-2"></i>Issue Inventory Stock</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stock-out-form">
                @csrf
                <input type="hidden" name="type" value="out">
                <div class="modal-body p-4 pt-0">
                    <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center gap-3">
                        <i class="fas fa-exclamation-circle fs-4"></i>
                        <div class="small">Issuing stock will decrease the available balance. Current: <strong>{{ $product->stock_quantity }} {{ $product->unit }}</strong></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Quantity to Deduct <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" class="form-control px-3 py-2 fs-5 fw-bold" required min="1" max="{{ $product->stock_quantity }}" placeholder="0">
                            <span class="input-group-text bg-light text-muted fw-bold">{{ $product->unit }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase">Remarks / Reference <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control px-3 py-2" rows="2" required placeholder="e.g. Sales to John, Damage, Internal Consumption..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="submit-out-btn" class="btn btn-danger px-4 fw-bold shadow-sm">Confirm Outbound</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>

    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
    
    .bg-success-light { background: rgba(34, 197, 94, 0.1); }
    .bg-danger-light { background: rgba(239, 68, 68, 0.1); }
    .bg-primary-light { background: rgba(59, 130, 246, 0.1); }
    .bg-warning-light { background: rgba(245, 158, 11, 0.1); }
    .bg-info-light { background: rgba(6, 182, 212, 0.1); }
    
    .pulse-danger {
        animation: pulse-danger-animation 2s infinite;
    }
    @keyframes pulse-danger-animation {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .fw-900 { font-weight: 900; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .extra-small { font-size: 0.75rem; }
</style>
@endsection

@section('scripts')
<!-- Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize jQuery Bootstrap Datepicker
    $('#date-filter').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom left'
    }).on('changeDate', function(e) {
        $('#date-clear-inline').show();
        applyFilters();
    });
    function handleTransactionSubmit(formId, btnId, modalId) {
        $(`#${formId}`).on('submit', function(e) {
            e.preventDefault();
            const btn = $(`#${btnId}`);
            const oldText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: "{{ route('master.products.stock.store', encrypt($product->id)) }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $(`#${modalId}`).modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error processing transaction.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: msg
                    });
                    btn.prop('disabled', false).html(oldText);
                }
            });
        });
    }

    handleTransactionSubmit('stock-in-form', 'submit-in-btn', 'stockInModal');
    handleTransactionSubmit('stock-out-form', 'submit-out-btn', 'stockOutModal');

    // Apply Filters Logic
    function applyFilters() {
        var typeFilter = $('.filter-btn.active').attr('data-filter');
        var dateFilter = $('#date-filter').val();

        if (dateFilter) {
            $('#date-clear-inline').show();
        } else {
            $('#date-clear-inline').hide();
        }

        var visibleCount = 0;
        $('.transaction-row').each(function() {
            var row = $(this);
            var matchType = (typeFilter === 'all' || row.attr('data-type') === typeFilter);
            var matchDate = (!dateFilter || row.attr('data-date') === dateFilter);

            if (matchType && matchDate) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });

        // Hide or show empty state based on visible count
        if (visibleCount === 0) {
            if ($('#empty-filter-msg').length === 0) {
                $('tbody').append('<tr id="empty-filter-msg"><td colspan="5" class="text-center py-5"><div class="opacity-25 mb-3"><i class="fas fa-search display-4"></i></div><p class="text-muted small">No transactions found matching your filters.</p></td></tr>');
            }
            $('#empty-filter-msg').show();
        } else {
            $('#empty-filter-msg').hide();
        }
    }

    $(document).on('click', '.filter-btn', function() {
        // Update active class
        $('.filter-btn').removeClass('active fw-bold bg-primary text-white').addClass('btn-light');
        $(this).addClass('active fw-bold bg-primary text-white').removeClass('btn-light');
        applyFilters();
    });

    // Handle manual input clear
    $('#date-filter').on('change input', applyFilters);
    
    // Inline clear button inside date input
    $('#date-clear-inline').on('click', function() {
        $('#date-filter').datepicker('clearDates');
        $('#date-clear-inline').hide();
        applyFilters();
    });

    // Laravel CSV Export - pass active filters as query params
    $('#export-csv-btn').on('click', function(e) {
        e.preventDefault();
        
        var dateFilter = $('#date-filter').val();
        
        // Validation: Ensure date is selected
        if (!dateFilter) {
            Swal.fire({
                icon: 'warning',
                title: 'Date Required',
                text: 'Please select a date before exporting the CSV report.',
                confirmButtonColor: '#16a34a'
            });
            return;
        }

        var baseUrl = "{{ route('master.products.stock.export', encrypt($product->id)) }}";
        var params  = new URLSearchParams();

        var typeFilter = $('.filter-btn.active').attr('data-filter');
        if (typeFilter && typeFilter !== 'all') {
            params.append('type', typeFilter);
        }

        params.append('date', dateFilter);

        var exportUrl = baseUrl + '?' + params.toString();
        window.location.href = exportUrl;
    });
});
</script>
@endsection
