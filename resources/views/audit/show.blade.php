@extends('layouts.dashboard')

@section('title', 'Audit Details - ' . $audit->audit_no)

@section('content')
<style>
    @media (max-width: 768px) {
        .content-header .justify-content-between { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .content-header .btn { width: 100%; }
        .page-title { font-size: 20px !important; }
        .page-subtitle { font-size: 11px !important; margin-left: 0 !important; padding-left: 0 !important; }
        .mismatch-val { font-size: 0.9rem !important; }
        .impact-val { font-size: 0.85rem !important; }
        .card-footer { flex-direction: column; gap: 10px; }
        .card-footer .btn { width: 100%; }
    }
</style>
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Audit / Session Details</span>
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('inventory.audit.index') }}" class="text-muted back-btn-minimal me-2" title="Back to Audits">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="page-title">{{ $audit->audit_no }}</h1>
                <span class="badge {{ $audit->status == 'approved' ? 'text-bg-success' : ($audit->status == 'submitted' ? 'text-bg-warning' : 'text-bg-secondary') }} ms-md-3">
                    {{ strtoupper($audit->status) }}
                </span>
            </div>
            
            @if($audit->status == 'submitted' && Auth::user()->hasRole('super_admin'))
            <form action="{{ route('inventory.audit.approve', encrypt($audit->id)) }}" method="POST" class="d-inline w-100 w-md-auto">
                @csrf
                <button type="submit" class="btn btn-success fw-bold" onclick="return confirm('Are you sure you want to approve this audit? main stock will be adjusted.')">
                    <i class="fas fa-check-circle me-1"></i> Approve & Adjust Stock
                </button>
            </form>
            @endif
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2 mb-0">Captured on {{ $audit->created_at->format('d M Y, h:i A') }} by {{ $audit->creator->name }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<div class="data-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0 text-muted fw-bold">Stock Reconciliation</h3>
        @if($audit->status == 'draft')
        <div class="text-muted small">Enter physical counts to calculate mismatches.</div>
        @endif
    </div>
    
    <form action="{{ route('inventory.audit.items.update', encrypt($audit->id)) }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="audit-table">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4 py-3 border-0">Product</th>
                        <th class="py-3 border-0 text-center">System Stock</th>
                        <th class="py-3 border-0 text-center" style="width: 120px;">Physical</th>
                        <th class="py-3 border-0 text-center">Mismatch</th>
                        <th class="py-3 border-0 text-center">Value Impact</th>
                        <th class="py-3 border-0">Reason / Note</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @foreach($audit->items as $item)
                    <tr class="{{ $item->mismatch_qty < 0 ? 'table-danger-light' : ($item->mismatch_qty > 0 ? 'table-success-light' : '') }}">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $item->inventory->name }}</div>
                            <div class="extra-small text-muted">{{ $item->inventory->code }}</div>
                        </td>
                        <td class="py-3 text-center fw-bold">
                            {{ $item->system_qty }} <span class="text-muted small">{{ $item->inventory->unit }}</span>
                        </td>
                        <td class="py-3 text-center">
                            @if($audit->status == 'draft')
                            <input type="number" name="items[{{ $item->id }}][physical_qty]" 
                                   value="{{ $item->physical_qty }}" 
                                   class="form-control form-control-sm text-center physical-input" 
                                   data-system="{{ $item->system_qty }}"
                                   data-price="{{ $item->unit_price ?? 0 }}"
                                   placeholder="0">
                            @else
                            <span class="fw-bold text-dark">{{ $item->physical_qty ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @php
                                $mismatch = $item->mismatch_qty ?? 0;
                                $color = $mismatch < 0 ? 'text-danger' : ($mismatch > 0 ? 'text-success' : 'text-muted');
                                $sign = $mismatch > 0 ? '+' : '';
                                $impact = $mismatch * ($item->unit_price ?? 0);
                            @endphp
                            <span class="fw-bold mismatch-val {{ $color }}">
                                {{ $sign }}{{ $mismatch }}
                            </span>
                        </td>
                        <td class="py-3 text-center">
                            @php
                                $impactColor = $impact < 0 ? 'text-danger' : ($impact > 0 ? 'text-success' : 'text-muted');
                            @endphp
                            <span class="fw-bold impact-val {{ $impactColor }}">
                                ₹{{ number_format($impact, 2) }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($audit->status == 'draft')
                            <select name="items[{{ $item->id }}][reason]" class="form-select form-select-sm">
                                <option value="">Select Reason...</option>
                                <option value="Counting Error" {{ $item->reason == 'Counting Error' ? 'selected' : '' }}>Counting Error</option>
                                <option value="Theft / Stealing" {{ $item->reason == 'Theft / Stealing' ? 'selected' : '' }}>Theft / Stealing</option>
                                <option value="Damaged Stock" {{ $item->reason == 'Damaged Stock' ? 'selected' : '' }}>Damaged Stock</option>
                                <option value="Data Entry Error" {{ $item->reason == 'Data Entry Error' ? 'selected' : '' }}>Data Entry Error</option>
                                <option value="Other" {{ $item->reason == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @else
                            <span class="text-muted italic">{{ $item->reason ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($audit->status == 'draft')
        <div class="card-footer bg-white py-4 d-flex justify-content-between">
            <button type="submit" class="btn btn-light border">
                <i class="fas fa-save me-1 small"></i> Save Draft
            </button>
            <button type="submit" name="submit_for_approval" value="1" class="btn btn-warning" onclick="return confirm('Submit this audit for approval? You wont be able to edit counts after this.')">
                <i class="fas fa-paper-plane me-1 small"></i> Submit for Approval
            </button>
        </div>
        @endif
    </form>
</div>

<style>
.table-danger-light { background-color: rgba(239, 68, 68, 0.05); }
.table-success-light { background-color: rgba(34, 197, 94, 0.05); }
.mismatch-val { font-size: 1.1rem; }
</style>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.physical-input').on('input', function() {
        const input = $(this);
        const row = input.closest('tr');
        const systemQty = parseInt(input.data('system'));
        const price = parseFloat(input.data('price')) || 0;
        const physicalQty = parseInt(input.val()) || 0;
        const mismatch = physicalQty - systemQty;
        const impact = mismatch * price;
        
        const mismatchSpan = row.find('.mismatch-val');
        const impactSpan = row.find('.impact-val');

        mismatchSpan.text((mismatch > 0 ? '+' : '') + mismatch);
        impactSpan.text('₹' + impact.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        row.removeClass('table-danger-light table-success-light');
        mismatchSpan.removeClass('text-danger text-success text-muted');
        impactSpan.removeClass('text-danger text-success text-muted');
        
        if (mismatch < 0) {
            row.addClass('table-danger-light');
            mismatchSpan.addClass('text-danger');
            impactSpan.addClass('text-danger');
        } else if (mismatch > 0) {
            row.addClass('table-success-light');
            mismatchSpan.addClass('text-success');
            impactSpan.addClass('text-success');
        } else {
            mismatchSpan.addClass('text-muted');
            impactSpan.addClass('text-muted');
        }
    });
});
</script>
@endsection
