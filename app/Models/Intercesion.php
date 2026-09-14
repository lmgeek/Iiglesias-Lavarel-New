<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intercesion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'intercesion';

    protected $fillable = [
        'calendar_day',
        'email',
        'notifications',
        'is_active',
    ];

    protected $casts = [
        'calendar_day' => 'integer',
        'notifications' => 'boolean',
        'is_active' => 'boolean',
    ];
}
