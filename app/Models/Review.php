<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reviews';
    protected $fillable = [
        'user_id',
        'ratingFor_id',
        'ratingFor_type',
        'rate',
        'feedback',
        'ipAddress'
    ];

    public function ratingFor()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
