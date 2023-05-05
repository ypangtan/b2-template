<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasTranslations;

use Helper;

class UserNotification extends Model
{
    use HasFactory, LogsActivity, HasTranslations;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'system_title',
        'system_content',
        'meta_data',
        'url_slug',
        'image',
        'type',
    ];

    protected $appends = [
        'path',
        'encrypted_id',
    ];

    public function user() {
        return $this->belongsTo( User::class, 'user_id' )->withTrashed();
    }

    public function getPathAttribute() {
        return $this->attributes['image'] ? asset( 'storage/' . $this->attributes['image'] ) : null;
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    public function getSystemTitleAttribute() {
        $metaData = json_decode( $this->attributes['meta_data'], true );
        return __( $this->attributes['system_title'], $metaData ? $metaData : [] );
    }

    public function getSystemContentAttribute() {
        $metaData = json_decode( $this->attributes['meta_data'], true );
        return __( $this->attributes['system_content'], $metaData ? $metaData : [] );
    }

    public $translatable = [ 'title', 'content' ];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'user_id',
        'title',
        'content',
        'system_title',
        'system_content',
        'meta_data',
        'url_slug',
        'image',
        'type',
    ];

    protected static $logName = 'user_notifications';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} user notification";
    }
}
