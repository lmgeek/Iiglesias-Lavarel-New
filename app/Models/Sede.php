<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sede extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sedes';

    protected $fillable = [
        'name',
        'address',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'sede_id');
    }
}
