<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_order_id',
        'inventory_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
