<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    CompanyService,
    CountryService,
};

class CountryController extends Controller
{
    public function index( Request $request ) {

        $this->data['header']['title'] = __( 'template.countries' );
        $this->data['content'] = 'admin.country.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.countries' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.countries' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function edit( Request $request ) {

        $this->data['header']['title'] = __( 'template.countries' );
        $this->data['content'] = 'admin.country.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.countries' ),
            'title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.countries' ) ) ] ),
            'mobile_title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.countries' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allCountries( Request $request ) {

        return CountryService::allCountries( $request );
    }

    public function oneCountry( Request $request ) {

        return CountryService::oneCountry( $request );
    }

    public function updateCountry( Request $request ) {

        return CountryService::updateCountry( $request );
    }

    public function updateCountryStatus( Request $request ) {

        return CountryService::updateCountryStatus( $request );
    }

    public function updateCountryStatusMultiple( Request $request ) {

        return CountryService::updateCountryStatusMultiple( $request );
    }
}
