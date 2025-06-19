<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTranslations;

class Country extends Model
{
    use HasFactory, HasTranslations;
    
    protected $hidden = [
        'id'
    ];

    protected $appends = [
        'encrypted_id',
    ];

    public $translatable = [ 'country_name' ];

    protected $fillable = [
        'country_name',
        'country_image',
        'currency_name',
        'currency_symbol',
        'iso_currency',
        'iso_alpha2_code',
        'iso_alpha3_code',
        'call_code',
        'status',
    ];
    
    public function getEncryptedIdAttribute() {
        return \Helper::encode( $this->attributes['id'] );
    }
}
