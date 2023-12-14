<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    public function country() {
        return $this->hasOne( Country::class, 'id', 'country_id' );
    }

    public function userDetail() {
        return $this->hasOne( UserDetail::class, 'user_id' );
    }

    public function referral() {
        return $this->hasOne( User::class, 'id', 'referral_id' );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'referral_id',
        'ranking_id',
        'old_id',
        'username',
        'email',
        'calling_code',
        'phone_number',
        'password',
        'invitation_code',
        'referral_structure',
        'capital',
        'status',
        'is_free',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'country_id',
        'referral_id',
        'ranking_id',
        'old_id',
        'username',
        'email',
        'calling_code',
        'phone_number',
        'password',
        'invitation_code',
        'referral_structure',
        'capital',
        'status',
        'is_free'
    ];

    protected static $logName = 'users';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} user";
    }
}
