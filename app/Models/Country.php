<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Traits\HasTranslations;

class Country extends Model
{
    use HasFactory, HasTranslations;
    
    protected $hidden = [
        'id'
    ];

    protected $appends = [
        'encrypted_id',
        'image_icon',
        'image_path',
    ];

    public $translatable = [ 'country_name' ];

    protected $fillable = [
        'country_name',
        'country_image',
        'country_icon',
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

    public function getImageIconAttribute() {

        if( !$this->attributes['country_icon'] ) {
            return null;
        }

        if ( Str::startsWith( $this->attributes['country_icon'], 'http' ) ) {
            return $this->attributes['country_icon'];
        }

        return asset('vendor/blade-flags/' . $this->attributes[ 'country_icon' ] );
    }

    public function getImagePathAttribute() {

        if( !$this->attributes['country_image'] ) {
            return null;
        }

        if ( Str::startsWith( $this->attributes['country_image'], 'http' ) ) {
            return $this->attributes['country_image'];
        }

        return asset('country/image_medium/' . $this->attributes[ 'country_image' ] );
    }

    public function getGotImageAttribute() {
        if( !$this->attributes[ 'country_image' ] ) {
            return false;
        }

        $imagePath = public_path('country/image_medium/' . $this->attributes[ 'country_image' ] );
        return File::exists($imagePath);
    }

    public function getGotIconAttribute() {
        if( !$this->attributes[ 'country_icon' ] ) {
            return false;
        }

        $imagePath = public_path('vendor/blade-flags/' . $this->attributes[ 'country_icon' ] );
        return File::exists($imagePath);
    }
}
