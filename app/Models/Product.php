<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasTranslations;

use Helper;

class Product extends Model
{
    use HasFactory, LogsActivity, HasTranslations;

    protected $fillable = [
        'parent_id',
        'sku',
        'title',
        'short_description',
        'description',
        'url_slug',
        'type',
        'thumbnail',
        'status',
    ];

    public function metadata() {
        return $this->hasMany( Metadata::class, 'type_id' )->where( 'type', 'product' );
    }

    public function productImages() {
        return $this->hasMany( ProductImage::class, 'product_id' );
    }

    public function productInventory() {
        return $this->hasOne( ProductInventory::class, 'product_id' );
    }

    public function productPrices() {
        return $this->hasMany( ProductPrice::class, 'product_id' );
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    public $translatable = [ 'title', 'short_description', 'description' ];

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'parent_id',
        'sku',
        'title',
        'short_description',
        'description',
        'url_slug',
        'type',
        'thumbnail',
        'status',
    ];

    protected static $logName = 'products';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} product";
    }
}
