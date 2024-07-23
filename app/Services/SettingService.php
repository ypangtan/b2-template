<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Validator,
};

use App\Models\{
    Option,
    Maintenance,
};

use PragmaRX\Google2FAQRCode\Google2FA;

class SettingService {

    public static function settings() {

        $settings = Option::whereIn( 'option_name', [

        ] )->get();

        return $settings;
    }

    public static function maintenanceSettings() {

        $maintenance = Maintenance::where( 'type', 3 )->first();

        return $maintenance;
    }

    public static function updateMaintenanceSetting( $request ) {

        Maintenance::lockForUpdate()->updateOrCreate( [
            'type' => 3
        ], [
            'status' => $request->status,
        ] );

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.settings' ) ) ] ),
        ] );
    }
}