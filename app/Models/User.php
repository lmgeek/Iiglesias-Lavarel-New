<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'fullname',
        'born_date',
        'sex',
        'email',
        'phone',
        'church',
        'mentor',
        'ministerial_range',
        'ministry_id',
        'sede_id',
        'celula',
        'doc_number',
        'lider_celula',
        'password',
        'google_id',
        'remember_token',
        'must_change_password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'celula' => 'integer',
        'lider_celula' => 'string',
        'password' => 'hashed',
    ];

    public function mentorUser()
    {
        return $this->belongsTo(User::class, 'mentor', 'id');
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function mentorRelationships()
    {
        return $this->hasMany(Relationship::class, 'mentor_id');
    }

    public function discipleRelationships()
    {
        return $this->hasMany(Relationship::class, 'disciple_id');
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function getActiveToken()
    {
        return $this->tokens()->where('expires_at', '>', now())->latest()->first();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function profileComplete(): bool
    {
        return filled($this->fullname)
            && filled($this->born_date)
            && in_array($this->sex, ['M', 'F'], true)
            && filled($this->phone)
            && preg_match('/^\d+$/', (string) $this->doc_number) === 1;
    }

    public function bornDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value) : null,
        );
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->born_date ? $this->born_date->age : null,
        );
    }

    public function whatsapp(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->phone)) {
                    return null;
                }

                $digits = preg_replace('/\D+/', '', $this->phone);
                if ($digits === '') {
                    return null;
                }

                if (! str_starts_with($digits, '54')) {
                    if (str_starts_with($digits, '0')) {
                        $digits = '54'.substr($digits, 1);
                    } else {
                        $digits = '549'.$digits;
                    }
                }

                $digits = str_replace('5490', '549', $digits);

                return 'https://wa.me/'.preg_replace('/\D+/', '', $digits);
            },
        );
    }

    public function isFacilitador(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lider_celula === 'Si',
        );
    }
}
