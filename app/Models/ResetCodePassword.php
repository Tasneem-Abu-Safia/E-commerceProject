<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResetCodePassword extends Model
{

    use HasFactory;
    protected $fillable = [
        'email',
        'code',
        'created_at',
    ];
}
