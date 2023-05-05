<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

class UserWallet extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'balance',
        'type',
    ];

    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }

    public function getListingBalanceAttribute() {
        return Helper::numberFormat( $this->attributes['balance'], 2 );
    }

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'user_id',
        'balance',
        'type',
    ];

    protected static $logName = 'user_wallets';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} user wallet";
    }
}
