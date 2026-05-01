<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    use HasFactory;

    protected $table = 'sales_orders';

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
        'notes',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft'      => '<span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#f1f5f9;color:#64748b;">Draft</span>',
            'confirmed'  => '<span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#dbeafe;color:#1d4ed8;">Confirmed</span>',
            'dispatched' => '<span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#fef3c7;color:#d97706;">Dispatched</span>',
            'completed'  => '<span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#dcfce7;color:#16a34a;">Completed</span>',
            'cancelled'  => '<span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#fee2e2;color:#b91c1c;">Cancelled</span>',
            default      => '<span>—</span>',
        };
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'SO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
