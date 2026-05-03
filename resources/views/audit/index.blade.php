@extends('layouts.dashboard')

@section('title', 'Stock Audits')

@section('content')
<style>
    @media (max-width: 768px) {
        .content-header .justify-content-between { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .content-header .btn { width: 100%; }
        .page-title { font-size: 20px !important; }
        .page-subtitle { font-size: 11px !important; margin-left: 0 !important; padding-left: 0 !important; }
    }
</style>
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Warehouse / Inventory Audit</span>
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('inventory.index') }}" class="text-muted back-btn-minimal me-2" title="Back to Inventory">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="page-title">Stock Audits</h1>
            </div>
            <a href="{{ route('inventory.audit.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="fas fa-plus me-1"></i> Start New Audit
            </a>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2 mb-0">Manage and review physical stock verification sessions.</p>
    </div>
</div>

<div class="data-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0 text-muted fw-bold">Audit History</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3 border-0">Audit No</th>
                    <th class="py-3 border-0">Date</th>
                    <th class="py-3 border-0">Started By</th>
                    <th class="py-3 border-0 text-center">Status</th>
                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($audits as $audit)
                <tr>
                    <td class="ps-4 py-3 fw-bold text-primary">{{ $audit->audit_no }}</td>
                    <td class="py-3">{{ $audit->created_at->format('d M Y, h:i A') }}</td>
                    <td class="py-3">{{ $audit->creator->name }}</td>
                    <td class="py-3 text-center">
                        @php
                            $badgeClass = [
                                'draft' => 'text-bg-secondary',
                                'submitted' => 'text-bg-warning',
                                'approved' => 'text-bg-success',
                                'cancelled' => 'text-bg-danger'
                            ][$audit->status] ?? 'text-bg-light';
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 small">{{ strtoupper($audit->status) }}</span>
                    </td>
                    <td class="pe-4 py-3 text-end">
                        <a href="{{ route('inventory.audit.show', encrypt($audit->id)) }}" class="btn btn-light border btn-sm">
                            <i class="fas fa-eye small me-1"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No audit records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($audits->hasPages())
    <div class="p-4 border-top">
        {{ $audits->links() }}
    </div>
    @endif
</div>
@endsection
