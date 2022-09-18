<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Crypt,
    Hash,
    Http,
    Storage
};

use App\Services\{
    UserService,
};

use App\Models\{
    User,
};

use Helper;

class UserController extends Controller {

    public function __construct() {}

    /**
     * 1. Request an OTP
     * 
     * @group User API
     * 
     * @bodyParam phone_number integer required The phone number for the new account. Example: 126373421
     * 
     */
    public function requestOtp( Request $request ) {

        return UserService::requestOtp( $request );
    }

    /**
     * 2. Create an user
     * 
     * @group User API
     * 
     * @bodyParam otp_code integer required The OTP code to verify register. Example: 487940
     * @bodyParam tmp_user string required The temporary user ID when request OTP. Example: aabbccdd11223344
     * @bodyParam username string required The username for the new account. Example: bobo lala
     * @bodyParam email email required The email for the new account. Example: bobo@gmail.com
     * @bodyParam password string required The password for the new account. Example: abcd1234
     * @bodyParam phone_number integer required The phone number for the new account. Example: 126373421
     * @bodyParam invitation_code string required The invitation code of referral. Example: AASSCC
     * @bodyParam device_type integer required The device type.<br>Example 1: iOS 2: Android. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createUser( Request $request ) {

        return UserService::createUser( $request );
    }
    
    /**
     * 3. Login an user - Username
     * 
     * @group User API
     * 
     * @bodyParam username string required The username for login. Example: test1
     * @bodyParam password string required The password for login. Example: abcd1234
     * @bodyParam device_type integer required The device type.<br>Example 1: iOS 2: Android. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createToken( Request $request ) {

        return UserService::createToken( $request );
    }

    /**
     * 4. Login an user - Social
     * 
     * @group User API
     * 
     * @bodyParam identifier string required The identifier returned from 3rd party social login API. Example: bobo@gmail.com
     * @bodyParam uuid string required The uuid return from 3rd party social login API. Example: dasd3-2dcvf-11133
     * @bodyParam platform integer required The 3rd party social login platform. <br>Example 1: Apple 2: Google. Example: 1
     * @bodyParam device_type integer required The device type.<br>Example 1: iOS 2: Android. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createTokenSocial( Request $request ) {

        return UserService::createTokenSocial( $request );
    }
}