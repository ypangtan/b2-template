<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductPrice extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
        'regular_price',
        'promo_price',
        'promo_date_from',
        'promo_date_to',
    ];

    public function getPromoEnabledAttribute() {

        if ( isset( $this->attributes['promo_date_to'] ) && $this->attributes['promo_date_to'] > date( 'Y-m-d H:i:s' ) ) {
            return 'yes';
        }

        return 'no';
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'product_id',
        'regular_price',
        'promo_price',
        'promo_date_from',
        'promo_date_to',
    ];

    protected static $logName = 'product_prices';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} product price";
    }
}
