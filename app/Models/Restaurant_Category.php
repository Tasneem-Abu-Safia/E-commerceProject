<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant_Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'category_restaurant';
    protected $fillable = [
        'category_id',
        'restaurant_id'
    ];

}
