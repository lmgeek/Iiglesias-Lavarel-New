<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $table = 'notification_logs';

    protected $fillable = [
        'event_id',
        'interval_type',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(CalendarEvent::class, 'event_id');
    }
}
