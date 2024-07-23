<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

use Carbon\Carbon;

class Maintenance extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'content',
        'day',
        'type',
        'status',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
    ];

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'content',
        'day',
        'type',
        'status',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
    ];

    protected static $logName = 'maintenances';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} maintenance";
    }
}
