@extends('layouts.dashboard')

@section('title', 'Start Stock Audit')

@section('content')
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Audit / New Session</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('inventory.audit.index') }}" class="text-muted back-btn-minimal me-2" title="Back to Audits">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">Start New Audit</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2">Initiate a physical stock verification process.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="data-card border-0 shadow-sm p-4">
            <h3 class="h6 mb-4 text-muted fw-bold">Audit Information</h3>
            <form action="{{ route('inventory.audit.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Audit Purpose / Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3" placeholder="e.g., Monthly stock check, Year-end verification..."></textarea>
                </div>
                
                <div class="alert alert-info small border-0 shadow-none mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Starting an audit will capture a <strong>snapshot</strong> of current system stock levels for all items.
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2">
                        <i class="fas fa-play me-1 small"></i> Initialize Audit Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
