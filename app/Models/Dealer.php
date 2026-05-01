<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'gstin',
        'dealer_type',
        'credit_limit',
        'is_active',
        'created_by'
    ];

    /**
     * Get the user who created the dealer.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
