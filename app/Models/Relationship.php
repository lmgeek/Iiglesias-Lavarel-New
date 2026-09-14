<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mentor_id',
        'disciple_id',
        'f_meet',
        'suspended',
        'why_suspended',
        'theme_meetings_id',
        'other_theme',
        'culminate',
        'initiative',
        'reading',
        'testimonials',
        'pray_together',
        'description',
        'uuid',
    ];

    protected $casts = [
        'f_meet' => 'datetime',
        'suspended' => 'string',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function disciple()
    {
        return $this->belongsTo(User::class, 'disciple_id');
    }

    public function theme()
    {
        return $this->belongsTo(MeetingsTheme::class, 'theme_meetings_id');
    }

    public function reports()
    {
        return $this->hasMany(ReportCelula::class, 'mentor_id', 'mentor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('suspended', 'No')->whereNull('deleted_at');
    }
}
