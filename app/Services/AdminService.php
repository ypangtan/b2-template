<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\{
    Admin,
    AdminMeta,
    AdminNotificationSeen,
    Role as RoleModel
};

use PragmaRX\Google2FAQRCode\Google2FA;

use Helper;

use Carbon\Carbon;

class AdminService {
    
    public function all( $request ) {

        $filter = false;

        $admin = Admin::select( 'admins.*', 'roles.name as role_name' );
        $admin->leftJoin( 'roles', 'admins.role', '=', 'roles.id' );

        if( !empty( $request->registered_date ) ) {
            if( str_contains( $request->registered_date, 'to' ) ) {
                $dates = explode( ' to ', $request->registered_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $admin->whereBetween( 'admins.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->registered_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $admin->whereBetween( 'admins.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }
        
        if( !empty( $request->username ) ) {
            $admin->where( 'username', $request->username );
            $filter = true;
        }

        if( !empty( $request->email ) ) {
            $admin->where( 'email', $request->email );
            $filter = true;
        }

        if( !empty( $request->role ) ) {
            $admin->where( 'roles.id', $request->role );
            $filter = true;
        }

        if( $request->input( 'order.0.column' ) != 0 ) {

            switch( $request->input( 'order.0.column' ) ) {
                case 1:
                    $admin->orderBy( 'created_at', $request->input( 'order.0.dir' ) );
                    break;
                case 2:
                    $admin->orderBy( 'username', $request->input( 'order.0.dir' ) );
                    break;
                case 3:
                    $admin->orderBy( 'email', $request->input( 'order.0.dir' ) );
                    break;
                case 4:
                    $admin->orderBy( 'role', $request->input( 'order.0.dir' ) );
                    break;
            }
        }

        $adminCount = $admin->count();

        $limit = $request->input( 'length' );
        $offset = $request->input( 'start' );
        
        $adminObject = $admin->skip( $offset )->take( $limit );
        $admins = $adminObject->get();

        $admin = Admin::select( \DB::raw( 'COUNT(id) as total' ) );

        if( !empty( $request->registered_date ) ) {
            if( str_contains( $request->registered_date, 'to' ) ) {
                $dates = explode( ' to ', $request->registered_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $admin->whereBetween( 'admins.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->registered_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $admin->whereBetween( 'admins.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        $admin = $admin->first();

        $data = array(
            'admins' => $admins,
            'draw' => $request->input( 'draw' ),
            'recordsFiltered' => $filter ? $adminCount : $admin->total,
            'recordsTotal' => Admin::select( \DB::raw( 'COUNT(id) as total' ) )->first()->total,
        );

        return $data;
    }

    public function one( $request ) {

        return Admin::find( $request->id );
    }

    public function create( $request ) {

        $request->validate( [
            'username' => 'required|max:25|unique:admins,username',
            'email' => 'required|max:25|unique:admins,email|email|regex:/(.+)@(.+)\.(.+)/i',
            'role' => 'required',
            'password' => 'required|min:8|max:255',
        ] );

        $createAdmin = Admin::create( [
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => \Hash::make( $request->password ),
        ] );

        $role_model = RoleModel::find( $request->role );

        $createAdmin->syncRoles( [ $role_model->name ] );

        return $createAdmin;
    }

    public function update( $request ) {

        $request->validate( [
            'username' => 'required|max:25|unique:admins,username,' . $request->id,
            'email' => 'required|max:25|unique:admins,email,' . $request->id.'|email|regex:/(.+)@(.+)\.(.+)/i',
            'role' => 'required',
            'password' => 'nullable|min:8|max:25',
        ] );

        $updateAdmin = Admin::find( $request->id );
        $updateAdmin->id = $request->id;
        $updateAdmin->username = $request->username;
        $updateAdmin->email = $request->email;
        $updateAdmin->role = $request->role;

        if( !empty( $request->password ) ) {
            $updateAdmin->password = \Hash::make( $request->password );
        }

        $role_model = RoleModel::find( $request->role );
        $updateAdmin->syncRoles( [ $role_model->name ] );
        $updateAdmin->save();

        return $updateAdmin;
    }

    public function verifyCode( $request ) {

        $request->validate( [
            'authentication_code' => [ 'bail', 'required', 'numeric', 'digits:6', function( $attribute, $value, $fail ) {

                $google2fa = new Google2FA();

                $secret = \Crypt::decryptString( auth()->user()->mfa_secret );
                $valid = $google2fa->verifyKey( $secret, $value );
                if ( !$valid ) {
                    $fail( __( 'setting.invalid_code' ) );
                }
            } ],
        ] );

        session( [
            'mfa-ed' => true
        ] );

        activity()
            ->useLog( 'admins' )
            ->withProperties( [
                'attributes' => [
                    'new_login' => date( 'Y-m-d H:i:s' ),
                ]
            ] )
            ->log( 'admin login' );

        return response()->json( [
            'status' => true,
        ] );
    }

    public function logoutLog() {

        activity()
            ->useLog( 'admins' )
            ->withProperties( [
                'attributes' => [
                    'logout' => date( 'Y-m-d H:i:s' ),
                ]
            ] )
            ->log( 'admin logout' );
    }

    public function updateNotificationBox( $request ) {
        
        AdminMeta::updateOrCreate( [
            'admin_id' => auth()->user()->id,
            'meta_key' => 'is_notification_box_opened'
        ], [
            'meta_value' => '1'
        ] );
    }

    public function updateNotificationSeen( $request ) {

        AdminNotificationSeen::firstOrCreate( [
            'admin_notification_id' => $request->id,
            'admin_id' => auth()->user()->id,
        ] );
    }
}