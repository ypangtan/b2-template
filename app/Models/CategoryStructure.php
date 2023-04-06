<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CategoryStructure extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'parent_id',
        'child_id',
        'level',
        'status',
    ];

    public function category() {
        return $this->belongsTo( Category::class, 'child_id' );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'parent_id',
        'child_id',
        'level',
        'status',
    ];

    protected static $logName = 'category_structures';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} category structure";
    }
}
