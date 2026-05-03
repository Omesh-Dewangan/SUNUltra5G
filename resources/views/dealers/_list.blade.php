<div class="table-responsive">
    <table class="table custom-table">
        <thead>
            <tr>
                <th>Dealer Details</th>
                <th>Type</th>
                <th>Contact info</th>
                <th>Credit Limit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dealers as $dealer)
            <tr>
                <td>
                    <div class="d-flex flex-column">
                        <span class="font-bold text-dark">{{ $dealer->name }}</span>
                        <small class="text-muted">GST: {{ $dealer->gstin ?? 'N/A' }}</small>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $dealer->dealer_type == 'distributor' ? 'bg-primary' : ($dealer->dealer_type == 'wholesaler' ? 'bg-info' : 'bg-secondary') }}">
                        {{ ucfirst($dealer->dealer_type) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span><i class="fas fa-phone-alt me-1 text-primary"></i> {{ $dealer->phone }}</span>
                        <small class="text-muted"><i class="fas fa-envelope me-1"></i> {{ $dealer->email ?? 'N/A' }}</small>
                    </div>
                </td>
                <td class="font-bold">
                    ₹{{ number_format($dealer->credit_limit, 2) }}
                </td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" 
                               {{ $dealer->is_active ? 'checked' : '' }}
                               onchange="toggleDealerStatus('{{ encrypt($dealer->id) }}')">
                        <label class="form-check-label">{{ $dealer->is_active ? 'Active' : 'Inactive' }}</label>
                    </div>
                </td>
                <td>
                    <div class="action-btn-group">
                        <button class="btn-icon btn-outline-primary" title="Edit" onclick='editDealer(@json($dealer), "{{ encrypt($dealer->id) }}")'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-outline-danger" title="Delete" onclick="deleteDealer('{{ encrypt($dealer->id) }}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No dealers found. Start by adding one!</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $dealers->links() }}
</div>
