<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

use Carbon\Carbon;

class UserKyc extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'review_by',
        'approved_by',
        'rejected_by',
        'country_id',
        'first_name',
        'last_name',
        'email',
        'calling_code',
        'phone_number',
        'gender',
        'date_of_birth',
        'ic_front',
        'ic_back',
        'remarks',
        'status_log',
        'review_at',
        'approved_at',
        'rejected_at',
        'status',
    ];

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'user_id',
        'review_by',
        'approved_by',
        'rejected_by',
        'country_id',
        'first_name',
        'last_name',
        'email',
        'calling_code',
        'phone_number',
        'gender',
        'date_of_birth',
        'ic_front',
        'ic_back',
        'remarks',
        'status_log',
        'review_at',
        'approved_at',
        'rejected_at',
        'status',
    ];

    protected static $logName = 'user_kycs';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} ";
    }
}
