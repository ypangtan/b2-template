<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

use Helper;

class Administrator extends Authenticatable
{
    use HasFactory, LogsActivity, HasRoles;

    protected $fillable = [
        'username',
        'email',
        'name',
        'role',
        'mfa_secret',
        'password',
        'status',
    ];

    public function role() {
        return $this->belongsTo( Role::class, 'role' );
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'username',
        'email',
        'name',
        'role',
        'mfa_secret',
        'password',
        'status',
    ];

    protected static $logName = 'administrators';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} administrator";
    }
}
