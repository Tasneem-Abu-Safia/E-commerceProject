<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'description',
        'price',
        'calories',
        'restaurant_id',
        'category_id',
        'subcategory_id',
        'active',
    ];

    public function restaurant()
    {
        return $this->belongsTo('App\Models\Restaurant');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function discount()
    {
        return $this->hasOne('App\Models\Discount');
    }

    public function subcategory()
    {
        return $this->belongsTo('App\Models\SubCategory');
    }

    public function reviews()
    {
        return $this->morphMany('App\Models\Review', 'ratingFor');
    }
}
