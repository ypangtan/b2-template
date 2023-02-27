<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasTranslations;

class AdminNotification extends Model
{
    use HasFactory, LogsActivity, SoftDeletes, HasTranslations;

    public function admin() {
        return $this->belongsTo( admin::class, 'admin_id' )->withTrashed();
    }

    protected $fillable = [
        'admin_id',
        'role_id',
        'title',
        'content',
        'system_title',
        'system_content',
        'meta_data',
        'image',
        'type',
    ];
    
    public $translatable = [ 'title', 'content' ];

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'admin_id',
        'role_id',
        'title',
        'content',
        'system_title',
        'system_content',
        'meta_data',
        'image',
        'type',
    ];

    protected static $logName = 'admin_notifications';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} admin notification";
    }
}
