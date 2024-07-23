<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    BankService,
    SettingService,
};

use Helper;

class SettingController extends Controller {

    public function index() {

        $this->data['header']['title'] = __( 'template.settings' );
        $this->data['content'] = 'admin.setting.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.settings' ),
            'title' => __( 'template.settings' ),
            'mobile_title' => __( 'template.settings' ),
        ];

        $this->data['data']['settings'] = SettingService::settings();

        return view( 'admin.main' )->with( $this->data );
    }

    public function settings( Request $request ) {

        return SettingService::settings();
    }

    public function maintenanceSettings( Request $request ) {

        return SettingService::maintenanceSettings();
    }

    public function updateMaintenanceSetting( Request $request ) {

        return SettingService::updateMaintenanceSetting( $request );
    }
}