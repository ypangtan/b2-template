<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

use Carbon\Carbon;

class MultiLanguageMessage extends Model
{
    use HasFactory, LogsActivity;

    protected $hidden = [
        'id'
    ];

    protected $appends = [
        'encrypted_id',
    ];
    
    protected $fillable = [
        'last_update_by',
        'module',
        'message_key',
        'text',
        'language',
    ];

    public function last_update_by() {
        return $this->belongsTo( Administrator::class, 'last_update_by' );
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'last_update_by',
        'module',
        'message_key',
        'text',
        'language',
    ];

    protected static $logName = 'multi_language_messages';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} ";
    }
}
