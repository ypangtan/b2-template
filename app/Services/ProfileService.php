<?php

namespace App\Services;

use App\Models\{
    Admin,
};

use Helper;

use Carbon\Carbon;

class ProfileService {

    public function update( $request ) {

        $adminID = auth()->user()->id;

        $request->validate( [
            'username' => 'required|max:25|unique:admins,username,' . $adminID,
            'email' => 'required|max:25|unique:admins,email,' . $adminID. '|email|regex:/(.+)@(.+)\.(.+)/i',
        ] );

        $updateAdmin = Admin::find( $adminID );
        $updateAdmin->username = $request->username;
        $updateAdmin->email = $request->email;
        $updateAdmin->save();
    }
}