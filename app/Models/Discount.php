<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'discount_percent',
        'active',
        'deadline',

    ];
    protected $table = 'discounts';

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
