@extends('layouts.dashboard')

@section('title', 'Dealers Management')

@section('content')
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Administration / Partners</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">Dealers Management</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2">Manage your distributors, wholesalers, and retail partners</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add New Dealer
    </button>
</div>

<div class="data-card">
    <div class="card-header">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="dealer-search" placeholder="Search by name, phone, or GSTIN..." onkeyup="filterDealers()">
        </div>
        <div class="filter-box">
            <select id="type-filter" onchange="filterDealers()" class="form-select">
                <option value="">All Types</option>
                <option value="distributor">Distributor</option>
                <option value="wholesaler">Wholesaler</option>
                <option value="retailer">Retailer</option>
            </select>
        </div>
    </div>

    <div id="dealers-table-container">
        @include('dealers._list', ['dealers' => $dealers])
    </div>
</div>

<!-- Create/Edit Dealer Modal -->
<div class="modal fade" id="dealer-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Add New Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dealer-form">
                @csrf
                <input type="hidden" id="dealer-id" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Dealer/Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="d-name" class="form-control" placeholder="Enter name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Dealer Type <span class="text-danger">*</span></label>
                                <select name="dealer_type" id="d-type" class="form-select" required>
                                    <option value="retailer">Retailer</option>
                                    <option value="wholesaler">Wholesaler</option>
                                    <option value="distributor">Distributor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Contact Person</label>
                                <input type="text" name="contact_person" id="d-contact" class="form-control" placeholder="Contact person name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="d-phone" class="form-control" placeholder="Phone number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Email Address</label>
                                <input type="email" name="email" id="d-email" class="form-control" placeholder="Email address">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">GSTIN</label>
                                <input type="text" name="gstin" id="d-gstin" class="form-control" placeholder="GST Number">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Credit Limit (₹)</label>
                                <input type="number" name="credit_limit" id="d-limit" class="form-control" placeholder="0.00" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Full Address</label>
                                <textarea name="address" id="d-address" class="form-control" rows="3" placeholder="Enter complete address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Save Dealer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let isEditing = false;

    function filterDealers() {
        const search = $('#dealer-search').val();
        const type = $('#type-filter').val();
        
        $.ajax({
            url: "{{ route('dealers.index') }}",
            data: { search, type },
            success: function(html) {
                $('#dealers-table-container').html(html);
            }
        });
    }

    function openCreateModal() {
        isEditing = false;
        $('#modal-title').text('Add New Dealer');
        $('#dealer-form')[0].reset();
        $('#dealer-id').val('');
        $('#dealer-modal').modal('show');
    }

    function editDealer(dealer, encryptedId) {
        isEditing = true;
        $('#modal-title').text('Edit Dealer');
        $('#dealer-id').val(encryptedId);
        $('#d-name').val(dealer.name);
        $('#d-type').val(dealer.dealer_type);
        $('#d-contact').val(dealer.contact_person);
        $('#d-phone').val(dealer.phone);
        $('#d-email').val(dealer.email);
        $('#d-gstin').val(dealer.gstin);
        $('#d-limit').val(dealer.credit_limit);
        $('#d-address').val(dealer.address);
        
        $('#dealer-modal').modal('show');
    }

    $('#dealer-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#dealer-id').val();
        const url = isEditing ? `/dealers/${id}` : "{{ route('dealers.store') }}";
        const method = isEditing ? 'PUT' : 'POST';
        
        const btn = $('#save-btn');
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                closeDealerModal();
                filterDealers();
                showNotification(response.message, 'success');
            },
            error: function(xhr) {
                const message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                showNotification(message, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false).text('Save Dealer');
            }
        });
    });

    function toggleDealerStatus(id) {
        $.post(`/dealers/${id}/toggle-status`, { _token: "{{ csrf_token() }}" }, function() {
            filterDealers();
            showNotification('Status updated!', 'success');
        });
    }

    function deleteDealer(id) {
        if (confirm('Are you sure you want to delete this dealer?')) {
            $.ajax({
                url: `/dealers/${id}`,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function() {
                    filterDealers();
                    showNotification('Dealer deleted successfully!', 'success');
                }
            });
        }
    }

    // Modal close on overlay click
    $('#dealer-modal').on('click', function(e) {
        if (e.target === this) closeDealerModal();
    });
</script>
@endsection
