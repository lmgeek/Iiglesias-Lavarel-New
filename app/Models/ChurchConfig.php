<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChurchConfig extends Model
{
    use HasFactory;

    protected $table = 'church_config';

    protected $fillable = [
        'church_name',
        'logo',
        'favicon',
        'login_bg',
        'phone',
        'email',
        'instagram',
        'facebook',
        'tiktok',
        'youtube',
    ];

    protected $casts = [
        'logo' => 'string',
        'favicon' => 'string',
    ];

    public static function getConfig(): self
    {
        return static::firstOrCreate([], [
            'church_name' => 'Catedral Cristiana',
            'logo' => null,
            'favicon' => null,
        ]);
    }
}
