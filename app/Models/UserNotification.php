<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// use App\Traits\HasTranslations;

use Helper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class UserNotification extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'system_title',
        'system_content',
        'platform_type',
        'meta_data',
        'url_slug',
        'image',
        'type',
    ];
    
    public function setTitleAttribute( $value ) {
        $translationKey = $value['key'];
        $data = $value['data'] ?? [];

        $nowLocale = App::getLocale();
        $languages = array_keys(Config::get('languages'));

        $translations = [];
        foreach ($languages as $lang) {
            App::setLocale($lang);
            $translations[$lang] = __( $translationKey, $data );
        }
        App::setLocale( $nowLocale );
        $this->attributes['title'] = json_encode($translations);
    }

    public function setContentAttribute( $value ) {
        $translationKey = $value['key'];
        $data = $value['data'] ?? [];

        $nowLocale = App::getLocale();
        $languages = array_keys(Config::get('languages'));
        $translations = [];
        foreach ($languages as $lang) {
            App::setLocale($lang);
            $translations[$lang] = __( $translationKey, $data );
        }

        App::setLocale( $nowLocale );
        $this->attributes['content'] = json_encode($translations);
    }

    public function getTitleAttribute() {
        $nowLocale = App::getLocale();
        $data = json_decode($this->attributes['title'], true);
        return isset( $data[ $nowLocale ] ) ? $data[ $nowLocale ] : array_values($data)[0];
    }

    public function getContentAttribute() {
        $nowLocale = App::getLocale();
        $data = json_decode($this->attributes['content'], true);
        return isset( $data[ $nowLocale ] ) ? $data[ $nowLocale ] : array_values($data)[0];
    }
    
    public function userNotificationUsers() {
        return $this->hasMany( UserNotificationUser::class, 'user_notification_id' );
    }

    public function UserNotificationSeens() {
        return $this->hasMany( UserNotificationSeen::class, 'user_notification_id' );
    }

    public function user() {
        return $this->belongsTo( User::class, 'user_id' )->withTrashed();
    }

    public function getPathAttribute() {
        return $this->attributes['image'] ? asset( 'storage/' . $this->attributes['image'] ) : null;
    }

    public function getDisplayStatusAttribute() {

        $status = [
            '1' => __( 'datatables.pending' ),
            '10' => __( 'datatables.published' ),
            '20' => __( 'promotion.unpublished' ),
        ];

        return $status[ $this->attributes['status'] ];
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    // public function getSystemTitleAttribute() {
    //     $metaData = json_decode( $this->attributes['meta_data'], true );
    //     return __( $this->attributes['system_title'], $metaData ? $metaData : [] );
    // }

    // public function getSystemContentAttribute() {
    //     $metaData = json_decode( $this->attributes['meta_data'], true );
    //     return __( $this->attributes['system_content'], $metaData ? $metaData : [] );
    // }

    // public $translatable = [ 'title', 'content' ];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'user_id',
        'title',
        'content',
        'system_title',
        'system_content',
        'platform_type',
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
