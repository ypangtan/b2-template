<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductImage extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
        'title',
        'file',
        'type',
        'file_type',
    ];

    public function getPathAttribute() {
        return $this->attributes['file'] ? asset( 'storage/'.$this->attributes['file'] ) : null;
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }
    
    protected static $logAttributes = [
        'product_id',
        'title',
        'file',
        'type',
        'file_type',
    ];

    protected static $logName = 'product_images';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} product image";
    }
}
