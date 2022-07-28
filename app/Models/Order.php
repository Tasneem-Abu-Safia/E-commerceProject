<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'totalPrice',
        'discount',
        'priceAfterDiscount',
        'status'
    ];

    public function orderDetails()
    {
        return $this->hasMany(Order_Details::class);
    }
}
