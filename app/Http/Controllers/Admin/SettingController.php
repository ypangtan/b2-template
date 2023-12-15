<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    SettingService
};

use Helper;

class SettingController extends Controller {

    public function index() {

        $this->data['header']['title'] = __( 'template.settings' );
        $this->data['content'] = 'admin.setting.index';

        return view( 'admin.main' )->with( $this->data );
    }
}