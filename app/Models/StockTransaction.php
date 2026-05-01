<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'price_per_unit',
        'remarks'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
