<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingsTheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'meetings_themes';

    protected $fillable = [
        'classname',
        'image',
        'image_delete_url',
        'youtube',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->classname;
    }

    public function relationships()
    {
        return $this->hasMany(Relationship::class, 'theme_meetings_id');
    }

    public function youtubeVideoId(): ?string
    {
        if (empty($this->youtube)) {
            return null;
        }

        if (preg_match('~(?<![\w-])(?:youtube(?:-nocookie)?\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)([\w-]{11})~', $this->youtube, $m)) {
            return $m[1];
        }

        return null;
    }

    public function youtubeEmbedUrl(): ?string
    {
        return $this->youtubeVideoId()
            ? 'https://www.youtube.com/embed/'.$this->youtubeVideoId()
            : null;
    }

    public function youtubeUrl(): ?string
    {
        if (empty($this->youtube)) {
            return null;
        }

        $youtube = trim($this->youtube);

        if (! str_starts_with($youtube, 'http://') && ! str_starts_with($youtube, 'https://')) {
            $youtube = 'https://'.$youtube;
        }

        return $this->youtubeVideoId()
            ? 'https://www.youtube.com/watch?v='.$this->youtubeVideoId()
            : $youtube;
    }

    public function getYoutubeAttribute($value): ?string
    {
        return $value ? trim($value) : null;
    }
}
