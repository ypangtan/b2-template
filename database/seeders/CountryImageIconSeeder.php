<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Country;
use Illuminate\Support\Str;
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
            $c->country_icon = 'country-' . $c->iso_alpha2_code . '.svg';
            $c->country_image = $c->iso_alpha2_code . '.png';
            $c->save();
        }
    }
}
