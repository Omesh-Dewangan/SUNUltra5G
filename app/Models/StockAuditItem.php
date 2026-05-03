<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAuditItem extends Model
{
    protected $fillable = [
        'audit_id',
        'inventory_id',
        'unit_price',
        'system_qty',
        'physical_qty',
        'mismatch_qty',
        'reason'
    ];

    public function audit()
    {
        return $this->belongsTo(StockAudit::class, 'audit_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
