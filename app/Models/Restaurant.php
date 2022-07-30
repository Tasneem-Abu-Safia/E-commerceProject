<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'restaurants';
    protected $fillable = [
        'name',
        'logo',
        'description',
        'phoneNumber',
        'address',
        'rating'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function reviews(){
        return $this->morphMany('App\Models\Review' , 'ratingFor');
    }
}
