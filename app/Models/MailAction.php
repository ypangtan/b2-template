<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

use Carbon\Carbon;
use App\Services\MailService;

class MailAction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'subject',
        'email',
        'data',
        'status',
    ];

    public function user() {
        return $this->belongsTo( User::class );
    }

    public function getMailAttribute() {
        $data = json_decode( $this->attributes['data'], true );
        $service = new MailService( $data );
        return $service->getView();
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'user_id',
        'subject',
        'email',
        'data',
        'status',
    ];

    protected static $logName = 'mail_actions';

    protected static $logOnlyDirty = false;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} ";
    }
}
