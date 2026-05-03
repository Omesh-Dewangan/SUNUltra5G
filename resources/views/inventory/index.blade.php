@extends('layouts.dashboard')

@section('title', 'Inventory Management')

@section('content')
<div class="row align-items-center mb-0 page-title-row">
    <div class="col-12 col-md-8">
        <span class="breadcrumb-item">Warehouse / Stock Control</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title" style="font-size: 22px;">Inventory Management</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2 mb-0">Live monitoring of Wires, Lights & Stock Levels.</p>
    </div>
</div>

<div class="data-card border-0 shadow-sm">
    <style>
        @media (max-width: 768px) {
            .page-title { font-size: 20px !important; }
            .page-subtitle { font-size: 12px !important; margin-left: 0 !important; padding-left: 0 !important; }
            .card-header { flex-direction: column; align-items: flex-start !important; gap: 15px; }
            .card-header .input-group { max-width: 100% !important; }
        }
    </style>
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="h6 mb-0 text-muted fw-bold">Stock Management</h3>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0 bg-light live-search-input" data-table="inventory-table" placeholder="Search stock by SKU, name...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="inventory-table">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3 border-0">Code</th>
                    <th class="py-3 border-0">Name & Category</th>
                    <th class="py-3 border-0 text-center">Wattage</th>
                    <th class="py-3 border-0">Specifications</th>
                    <th class="py-3 border-0 text-center">Current Stock</th>
                    <th class="pe-4 py-3 border-0 text-end">Adjust Stock</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($inventories as $item)
                <tr>
                    <td class="ps-4 py-3 fw-bold text-primary">{{ $item->code }}</td>
                    <td class="py-3">
                        <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">{{ $item->name }}</div>
                        <div class="extra-small text-muted">{{ $item->category->name }}</div>
                    </td>
                    <td class="py-3 text-center fw-bold">{{ $item->wattage ?? '-' }}</td>
                    <td class="py-3">
                        @if($item->specifications)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($item->specifications as $key => $val)
                                    <span class="badge text-bg-light border small text-dark fw-normal" style="font-size: 10px;">
                                        <span class="text-muted">{{ $key }}:</span> {{ $val }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="py-3 text-center">
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-1">
                                <span class="h6 fw-bold text-dark mb-0">{{ $item->stock_quantity }}</span>
                                <span class="small text-muted ms-1">{{ $item->unit }}</span>
                            </div>
                            @if($item->stock_quantity == 0)
                                <span class="badge text-bg-danger px-2" style="font-size: 9px;">OUT OF STOCK</span>
                            @elseif($item->isLowStock())
                                <span class="badge text-bg-warning px-2" style="font-size: 9px;">LOW STOCK</span>
                            @else
                                <span class="badge text-bg-success px-2" style="font-size: 9px;">IN STOCK</span>
                            @endif
                        </div>
                    </td>
                    <td class="pe-4 py-3 text-end">
                        <div class="d-inline-flex align-items-center gap-1">
                            <button class="btn btn-success btn-sm adjust-btn p-0 d-flex align-items-center justify-content-center" 
                                    data-id="{{ $item->id }}" data-type="in" style="width: 24px; height: 24px;">+</button>
                            
                            <input type="number" id="qty-{{ $item->id }}" value="1" min="1" 
                                   class="form-control form-control-sm text-center px-1" style="width: 45px;">
                            
                            <button class="btn btn-danger btn-sm adjust-btn p-0 d-flex align-items-center justify-content-center" 
                                    data-id="{{ $item->id }}" data-type="out" style="width: 24px; height: 24px;">-</button>
                            
                            <a href="{{ route('master.products.stock', encrypt($item->id)) }}" class="btn btn-light border btn-sm ms-1" title="History">
                                <i class="fas fa-history small text-primary"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($inventories->hasPages())
    <div class="p-4 border-top">
        {{ $inventories->links() }}
    </div>
    @endif
</div>

<div id="status-toast" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; font-weight: 600; display: none; z-index: 9999;"></div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.adjust-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const type = btn.data('type');
        const quantity = parseInt($(`#qty-${id}`).val());
        const toast = $('#status-toast');
        const productName = btn.closest('tr').find('div[style="font-weight: 600;"]').text().trim();

        if (isNaN(quantity) || quantity < 1) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Quantity',
                text: 'Please enter a valid quantity of 1 or more.'
            });
            return;
        }

        const actionText = type === 'in' ? 'Add' : 'Deduct';
        const color = type === 'in' ? '#22c55e' : '#ef4444';

        Swal.fire({
            title: 'Confirm Adjustment',
            html: `Are you sure you want to <b>${actionText} ${quantity}</b> unit(s) for <b>${productName}</b>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#64748b',
            confirmButtonText: `Yes, ${actionText} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('inventory.adjust_stock') }}",
                    method: 'POST',
                    data: {
                        inventory_id: id,
                        quantity: quantity,
                        type: type,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to adjust stock.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: msg
                        });
                        btn.prop('disabled', false);
                    }
                });
            }
        });
    });
});
</script>
@endsection
