<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Helper;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'referral_id',
        'username',
        'email',
        'calling_code',
        'phone_number',
        'password',
        'secuirty_pin',
        'invitation_code',
        'referral_structure',
        'role',
        'status',
    ];
    
    public function getAssetAttribute() {
        $balances = $this->wallet()->where('type', 1)->first();
        $balances = $balances->balance;
        return \Helper::numberFormat($balances, 2, true);
    }

    public function allDownlines(){
        $downlines = $this->groups()->with('user')->get()->pluck('user'); 
        $downlines = $downlines->flatten()->filter();

        return $downlines;
    }

    public function downlines() {
        return $this->hasMany( User::class, 'referral_id' )
            ->where( 'status', 10 );
    }
    
    public function groups() {
        return $this->hasMany( UserStructure::class, 'referral_id' );
    }

    public function upline() {
        return $this->belongsTo( User::class, 'referral_id' );
    }

    public function wallet() {
        return $this->hasMany( UserWallet::class, 'user_id' );
    }

    public function kyc() {
        return $this->hasOne( UserKyc::class, 'user_id' );
    }

    public function team() {

        $referralArray = !empty( $this->attributes['referral_structure'] ) 
            ? explode( '|', $this->attributes['referral_structure'] ) 
            : [];
    
        $downlinesArray = $this->allDownlines()->pluck('id')->toArray(); 
    
        $idArray = array_merge($referralArray, $downlinesArray); 
        
        $member = User::whereIn('id', $idArray);

        return $member;
    }


    public function country() {
        return $this->belongsTo( Country::class, 'country_id' );
    }

    public function userDetail() {
        return $this->hasOne( UserDetail::class, 'user_id' );
    }

    public function referral() {
        return $this->belongsTo( User::class, 'referral_id' );
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getEncryptedIdAttribute() {
        return Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }

    protected static $logAttributes = [
        'country_id',
        'referral_id',
        'username',
        'email',
        'calling_code',
        'phone_number',
        'password',
        'invitation_code',
        'referral_structure',
        'role',
        'status',
    ];

    protected static $logName = 'users';

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable();
    }

    public function getDescriptionForEvent( string $eventName ): string {
        return "{$eventName} user";
    }
}
