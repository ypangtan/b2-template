<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

use Carbon\Carbon;

class OtpAction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'country_id',
        'calling_code',
        'phone_number',
        'otp_code',
        'email',
        'status',
        'expire_on',
    ];

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'country_id',
        'calling_code',
        'phone_number',
        'otp_code',
        'email',
        'status',
        'expire_on',
    ];

    protected static $logName = 'otpAction';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} ";
    }
}
