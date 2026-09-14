<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCelula extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reports_celula';

    protected $fillable = [
        'mentor_id',
        'f_meet',
        'celula',
        'suspended',
        'why_suspended',
        'lider',
        'message_title',
        'who_meet',
        'format',
        'people_qty',
        'new_people_qty',
        'mentoring',
        'observations',
    ];

    protected $casts = [
        'f_meet' => 'datetime',
        'people_qty' => 'integer',
        'new_people_qty' => 'integer',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
