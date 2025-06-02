<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    MailActionService,
    MultiLanguageService,
    Service,
};

class MultiLanguageController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.multi_languages' );
        $this->data['content'] = 'admin.multi_language.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.multi_languages' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.multi_languages' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function add() {

        $this->data['header']['title'] = __( 'template.multi_languages' );
        $this->data['content'] = 'admin.multi_language.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.multi_languages' ),
            'title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.multi_languages' ) ) ] ),
            'mobile_title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.multi_languages' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allMultiLanguages( Request $request ) {

        return MultiLanguageService::allMultiLanguages( $request );
    }

    public function oneMultiLanguage( Request $request ) {

        return MultiLanguageService::oneMultiLanguage( $request );
    }

    public function createMultiLanguageAdmin( Request $request ) {

        return MultiLanguageService::createMultiLanguageAdmin( $request );
    }

    public function updateMultiLanguageAdmin( Request $request ) {

        return MultiLanguageService::updateMultiLanguageAdmin( $request );
    }
}
