@extends('layouts.dashboard')

@section('title', 'New Sales Order')

@section('content')
<style>
    .select2-container--default .select2-selection--single {
        height: 40px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background-color: var(--content-bg);
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-dark);
        font-size: 14px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; }
    .select2-dropdown {
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        border-radius: 8px;
    }
    .select2-search__field {
        border-radius: 6px !important;
        border: 1px solid var(--border-color) !important;
        background-color: var(--content-bg) !important;
        color: var(--text-dark) !important;
    }
</style>
<div class="row align-items-center mb-4 g-3">
    <div class="col-12 col-md-6">
        <h1 class="h3 fw-bold text-dark mb-1">New Sales Order</h1>
        <p class="text-muted mb-0">Fill in customer details and add products.</p>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<form id="sales-order-form">
    <div class="row g-4">
        <!-- Left: Customer & Items -->
        <div class="col-12 col-lg-8">
            <!-- Customer Details -->
            <div class="data-card mb-4 shadow-sm border-0">
                <h3 class="h5 fw-bold mb-4">Customer Details</h3>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Customer Name *</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control px-3 py-2" required placeholder="Enter customer name">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control px-3 py-2" placeholder="Mobile number">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Address</label>
                        <input type="text" name="customer_address" id="customer_address" class="form-control px-3 py-2" placeholder="City / Area">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control px-3 py-2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="data-card shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 fw-bold mb-0">Order Items</h3>
                    <button type="button" onclick="addItemRow()" class="btn btn-primary btn-sm px-3 fw-bold">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="items-table">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="border-0 py-3">Product</th>
                                <th class="border-0 py-3 text-center" style="width: 100px;">Qty</th>
                                <th class="border-0 py-3 text-end" style="width: 140px;">Unit Price (₹)</th>
                                <th class="border-0 py-3 text-end" style="width: 140px;">Total (₹)</th>
                                <th class="border-0 py-3" style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <!-- Rows added dynamically -->
                        </tbody>
                    </table>
                </div>

                <div id="no-items-msg" class="py-5 text-center text-muted">
                    <i class="fas fa-shopping-basket d-block mb-3 opacity-25 h1"></i>
                    Click "Add Product" to add items to this order.
                </div>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="col-12 col-lg-4">
            <div class="data-card shadow-sm border-0 sticky-top" style="top: 100px; z-index: 100;">
                <h3 class="h5 fw-bold mb-4">Order Summary</h3>
                <div id="summary-items" class="d-flex flex-column gap-3 mb-4 min-vh-10" style="min-height: 60px;">
                    <p class="text-muted small mb-0">No items added yet.</p>
                </div>
                
                <div class="border-top pt-4 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h6 fw-bold mb-0">Grand Total</span>
                        <span class="h4 fw-bold text-primary mb-0">₹<span id="grand-total">0.00</span></span>
                    </div>
                </div>
                
                <button type="button" onclick="submitOrder()" class="btn btn-primary w-100 mt-4 py-3 fw-bold rounded-3 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Order
                </button>
                
                <p class="extra-small text-muted text-center mt-3 mb-0">
                    You can confirm & dispatch from the Order Detail page.
                </p>
            </div>
        </div>
    </div>
</form>

<!-- Toast -->
<div id="toast" style="display:none; position:fixed; bottom:24px; right:24px; padding:14px 24px; border-radius:10px; font-size:14px; font-weight:600; color:white; z-index:9999; min-width:280px;"></div>
@endsection

@section('scripts')
<script>
const products = @json($products);
let rowIndex = 0;

function addItemRow() {
    rowIndex++;
    const options = products.map(p =>
        `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock_quantity}" data-unit="${p.unit}">${p.name} (Stock: ${p.stock_quantity} ${p.unit})</option>`
    ).join('');

    const row = `
    <tr id="item-row-${rowIndex}" style="border-bottom: 1px solid var(--border-color);">
        <td style="padding: 10px 0;">
            <select name="items[${rowIndex}][inventory_id]" id="select-${rowIndex}" class="select2-item form-select">
                <option value="">-- Select Product --</option>
                ${options}
            </select>
            <input type="hidden" name="items[${rowIndex}][inventory_id]" id="pid-${rowIndex}" value="">
        </td>
        <td class="text-center py-3">
            <input type="number" name="items[${rowIndex}][quantity]" id="qty-${rowIndex}" min="1" value="1"
                   onchange="recalcRow(${rowIndex})"
                   class="form-control form-control-sm text-center mx-auto" style="width: 70px;">
        </td>
        <td class="text-end py-3">
            <input type="number" name="items[${rowIndex}][unit_price]" id="price-${rowIndex}" min="0" step="0.01" value="0"
                   onchange="recalcRow(${rowIndex})"
                   class="form-control form-control-sm text-end ms-auto" style="width: 110px;">
        </td>
        <td class="text-end py-3 fw-bold text-dark" id="total-${rowIndex}">₹0.00</td>
        <td class="text-center py-3">
            <button type="button" onclick="removeRow(${rowIndex})" class="btn btn-link text-danger p-0">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;

    $('#items-body').append(row);
    
    // Initialize Select2 for this specific row
    $(`#select-${rowIndex}`).select2({
        placeholder: "-- Select Product --",
        width: '100%'
    }).on('change', function() {
        onProductChange(this, rowIndex);
    });

    $('#no-items-msg').hide();
    updateSummary();
}

function onProductChange(select, idx) {
    const opt = select.options[select.selectedIndex];
    const price = parseFloat(opt.dataset.price) || 0;
    $(`#pid-${idx}`).val(opt.value);
    $(`#price-${idx}`).val(price.toFixed(2));
    recalcRow(idx);
}

function recalcRow(idx) {
    const qty   = parseFloat($(`#qty-${idx}`).val()) || 0;
    const price = parseFloat($(`#price-${idx}`).val()) || 0;
    const total = qty * price;
    $(`#total-${idx}`).text('₹' + total.toFixed(2));
    updateSummary();
}

function removeRow(idx) {
    $(`#item-row-${idx}`).remove();
    if ($('#items-body tr').length === 0) $('#no-items-msg').show();
    updateSummary();
}

function updateSummary() {
    let grand = 0;
    let html = '';
    $('#items-body tr').each(function() {
        const id    = $(this).attr('id').replace('item-row-', '');
        const name  = $(`#item-row-${id} select option:selected`).text();
        const total = parseFloat($(`#total-${id}`).text().replace('₹', '')) || 0;
        if (name && name !== '-- Select Product --') {
            html += `<div class="d-flex justify-content-between small mb-1">
                        <span class="text-secondary">${name.split('(')[0].trim()}</span>
                        <span class="fw-bold text-dark">₹${total.toFixed(2)}</span>
                     </div>`;
            grand += total;
        }
    });
    $('#summary-items').html(html || '<p class="text-muted small mb-0">No items added yet.</p>');
    $('#grand-total').text(grand.toFixed(2));
}

function submitOrder() {
    const rows = $('#items-body tr');
    const customerName = $('#customer_name').val().trim();

    // 1. Customer Validation
    if (!customerName) {
        showToast('Customer name is required.', '#ef4444');
        $('#customer_name').focus();
        return;
    }

    // 2. Empty Items Validation
    if (rows.length === 0) {
        showToast('Please add at least one product to the order.', '#ef4444');
        return;
    }

    const items = [];
    let isValid = true;
    let errorMessage = '';

    rows.each(function() {
        const id = $(this).attr('id').replace('item-row-', '');
        const select = $(`#select-${id}`);
        const inv_id = $(`#pid-${id}`).val();
        const qty = parseInt($(`#qty-${id}`).val()) || 0;
        const price = parseFloat($(`#price-${id}`).val()) || 0;
        
        // Get stock info from data attributes of selected option
        const selectedOpt = select.find('option:selected');
        const stock = parseInt(selectedOpt.data('stock')) || 0;
        const productName = selectedOpt.text().split('(')[0].trim();

        // 3. Product Selection Validation
        if (!inv_id) {
            isValid = false;
            errorMessage = 'Please select a product for all rows.';
            select.focus();
            return false;
        }

        // 4. Quantity Validation
        if (qty <= 0) {
            isValid = false;
            errorMessage = `Quantity for ${productName} must be greater than 0.`;
            $(`#qty-${id}`).focus();
            return false;
        }

        // 5. Stock Limit Validation
        if (qty > stock) {
            isValid = false;
            errorMessage = `Insufficient stock for ${productName}. Available: ${stock}.`;
            $(`#qty-${id}`).focus();
            return false;
        }

        items.push({ 
            inventory_id: inv_id, 
            quantity: qty, 
            unit_price: price 
        });
    });

    if (!isValid) {
        showToast(errorMessage, '#ef4444');
        return;
    }

    // 6. Final Submission
    const submitBtn = $('button[onclick="submitOrder()"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: '{{ route("sales.store") }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        contentType: 'application/json',
        data: JSON.stringify({
            customer_name:    customerName,
            customer_phone:   $('#customer_phone').val(),
            customer_address: $('#customer_address').val(),
            notes:            $('#notes').val(),
            items:            items
        }),
        success: function(res) {
            if (res.success) {
                showToast(res.message, '#16a34a');
                setTimeout(() => window.location.href = res.redirect, 1000);
            } else {
                showToast(res.message, '#ef4444');
                submitBtn.prop('disabled', false).html('<i class="fas fa-save" style="margin-right: 8px;"></i>Save as Draft');
            }
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Failed to save order. Please check your data.';
            showToast(msg, '#ef4444');
            submitBtn.prop('disabled', false).html('<i class="fas fa-save" style="margin-right: 8px;"></i>Save as Draft');
        }
    });
}

function showToast(msg, bg) {
    const t = $('#toast');
    t.text(msg).css('background', bg).fadeIn();
    setTimeout(() => t.fadeOut(), 4000);
}
</script>
@endsection
