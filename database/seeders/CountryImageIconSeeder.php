<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\{
    Crypt,
    DB,
    Hash,
    Http,
    Validator,
};

use Illuminate\Database\Seeder;

class CountryImageIconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = Country::all();
        foreach( $countries as $c ) {
            
            $code = Str::lower( $c->iso_alpha2_code );
            $country_icon = 'country-' . $code . '.svg';
            $country_image = $c->iso_alpha2_code . '.png';

            $c->country_icon = self::getGotIconAttribute( $country_icon ) ? asset('vendor/blade-flags/' . $country_icon ) : '';
            $c->country_image = self::getGotImageAttribute( $country_image ) ? asset('country/image_medium/' . $country_image ) : '';
            $c->save();
        }
    }

    public function getGotImageAttribute( $country_image ) {
        $imagePath = public_path('country/image_medium/' . $country_image );
        return File::exists($imagePath);
    }

    public function getGotIconAttribute( $country_icon ) {

        $imagePath = public_path('vendor/blade-flags/' . $country_icon );
        return File::exists($imagePath);
    }
}
