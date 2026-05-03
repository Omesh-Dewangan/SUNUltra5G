<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAudit extends Model
{
    protected $fillable = [
        'audit_no',
        'status',
        'created_by',
        'approved_by',
        'remarks',
        'completed_at'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockAuditItem::class, 'audit_id');
    }
}
