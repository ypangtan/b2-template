<?php

namespace App\Services;

use App\Models\{
    Administrator,
};

use Helper;

use Carbon\Carbon;

class ProfileService {

    public function update( $request ) {

        $adminID = auth()->user()->id;

        $request->validate( [
            'username' => 'required|max:25|unique:administrators,username,' . $adminID,
            'email' => 'required|max:25|unique:administrators,email,' . $adminID. '|email|regex:/(.+)@(.+)\.(.+)/i',
        ] );

        $updateAdmin = Administrator::find( $adminID );
        $updateAdmin->username = $request->username;
        $updateAdmin->email = $request->email;
        $updateAdmin->save();
    }
}