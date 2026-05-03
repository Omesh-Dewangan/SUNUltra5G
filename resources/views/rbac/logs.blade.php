@extends('layouts.dashboard')

@section('title', 'System Activity Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold text-dark-mode-white">System Activity Logs</h1>
        <p class="text-muted small mb-0">Track and manage administrative actions and RBAC changes.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light bg-opacity-50">
                    <tr>
                        <th class="ps-4 py-3 text-muted small text-uppercase fw-bold">Timestamp</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold">Admin</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold">Action</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold">Resource</th>
                        <th class="py-3 text-muted small text-uppercase fw-bold">Details</th>
                        <th class="pe-4 py-3 text-muted small text-uppercase fw-bold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark-mode-white small">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-muted tiny">{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                </div>
                                <span class="small fw-semibold text-dark-mode-white">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($log->action) {
                                    'DELETE_ROLE', 'DELETE_USER' => 'bg-danger',
                                    'CREATE_ROLE', 'CREATE_USER' => 'bg-success',
                                    'RESTORE_ROLE' => 'bg-info',
                                    default => 'bg-primary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} bg-opacity-10 {{ str_replace('bg-', 'text-', $badgeClass) }} border border-{{ str_replace('bg-', '', $badgeClass) }} border-opacity-25 px-2 py-1" style="font-size: 10px;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $log->model }}</span>
                        </td>
                        <td style="max-width: 300px;">
                            <div class="text-truncate-2 small text-secondary">
                                @if(is_array($log->details))
                                    @foreach($log->details as $key => $val)
                                        @if($key !== 'permissions' && $key !== 'permission_ids')
                                            <span class="fw-bold text-dark-mode-white">{{ ucfirst($key) }}:</span> 
                                            {{ is_array($val) ? json_encode($val) : $val }}@if(!$loop->last), @endif
                                        @endif
                                    @endforeach
                                    @if(isset($log->details['permissions']))
                                        <div class="mt-1">
                                            <span class="fw-bold text-dark-mode-white small">Permissions:</span>
                                            <span class="tiny text-muted">{{ is_array($log->details['permissions']) ? implode(', ', $log->details['permissions']) : $log->details['permissions'] }}</span>
                                        </div>
                                    @endif
                                @else
                                    {{ $log->details }}
                                @endif
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            @if(str_starts_with($log->action, 'DELETE_'))
                                <button onclick="restoreResource('{{ encrypt($log->id) }}', '{{ $log->details['name'] ?? 'Resource' }}')" class="btn btn-sm btn-outline-info rounded-pill px-3" style="font-size: 11px;">
                                    <i class="fas fa-undo me-1"></i> Revert
                                </button>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                            <p>No activity logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="p-4 border-top">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .tiny { font-size: 10px; }
    
    /* Dark mode adjustments for logs */
    [data-theme="dark"] .text-dark-mode-white { color: #f1f5f9 !important; }
    [data-theme="dark"] .bg-light { background-color: rgba(255,255,255,0.02) !important; }
</style>
@endsection

@section('scripts')
<script>
function restoreResource(encryptedId, resourceName) {
    Swal.fire({
        title: 'Revert Deletion?',
        text: `Do you want to restore "${resourceName}" with its original details?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0dcaf0',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Restore it',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `{{ url('/rbac/logs') }}/${encryptedId}/restore`,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.responseJSON?.message || 'Server error'}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value.status) {
            Swal.fire({
                icon: 'success',
                title: 'Restored!',
                text: result.value.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}
</script>
@endsection
