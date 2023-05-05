<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

class UserWalletTransaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_wallet_id',
        'user_id',
        'opening_balance',
        'amount',
        'closing_balance',
        'remark',
        'type',
        'transaction_type',
        'status',
    ];

    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }

    public function getConvertedRemarkAttribute() {
        if ( str_contains( $this->attributes['remark'], '}##' ) ) {
            $rawStatement = explode( '}##', $this->attributes['remark'] );
            $remark = str_replace( '##{', '', $rawStatement[0] );
            return __( 'wallet.' . $remark ) . $rawStatement[1];
        }

        return $this->attributes['remark'];
    }

    public function getListingAmountAttribute() {
        return Helper::numberFormat( $this->attributes['amount'], 2 );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'user_wallet_id',
        'user_id',
        'opening_balance',
        'amount',
        'closing_balance',
        'remark',
        'type',
        'transaction_type',
        'status',
    ];

    protected static $logName = 'user_wallet_transactions';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} user wallet transaction";
    }
}
