<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_price',
    ];

    // العلاقة مع العناصر (OrderItems)
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // العلاقة مع الزبون
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

