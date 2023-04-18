<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasTranslations;

use Helper;

class Category extends Model
{
    use HasFactory, LogsActivity, HasTranslations;

    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'thumbnail',
        'url_slug',
        'structure',
        'sort',
        'status',
        'type',
    ];

    public function childrens() {
        return $this->hasMany( CategoryStructure::class, 'parent_id' )->where( 'status', 10 )->orderBy( 'level', 'ASC' );
    }

    public function products() {
        return $this->hasMany( ProductCategory::class, 'category_id' );
    }

    public function getPathAttribute() {
        return $this->attributes['thumbnail'] ? asset( 'storage/'.$this->attributes['thumbnail'] ) : null;
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    public $translatable = [ 'title', 'description' ];

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'parent_id',
        'title',
        'description',
        'thumbnail',
        'url_slug',
        'structure',
        'sort',
        'status',
        'type',
    ];

    protected static $logName = 'categories';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} category";
    }
}
