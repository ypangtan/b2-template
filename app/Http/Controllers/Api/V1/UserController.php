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
     * <strong>request_type</strong><br>
     * 1: Register<br>
     * 
     * @group User API
     * 
     * @bodyParam email email required The email the new account. Example: johnwick@gmail.com
     * @bodyParam type integer required The request type for OTP. Example: 1
     * 
     */
    public function requestOtp( Request $request ) {

        return UserService::requestOtp( $request );
    }

    /**
     * 2. Resend an OTP
     * 
     * <strong>request_type</strong><br>
     * 2: Resend<br>
     * 
     * @group User API
     * 
     * @bodyParam tmp_user string required The temporary user ID during request OTP. Example: eyJpdiI...
     * @bodyParam type integer required The request type for OTP. Example: 1
     * 
     */
    public function resendOtp( Request $request ) {

        return UserService::requestOtp( $request );
    }

    /**
     * 3. Create an user
     * 
     * <strong>device_type</strong><br>
     * 1: iOS<br>
     * 2: Android<br>
     * 3: Web<br>
     * 
     * @group User API
     * 
     * @bodyParam otp_code integer required The OTP code to verify register. Example: 487940
     * @bodyParam tmp_user string required The temporary user ID during request OTP. Example: eyJpdiI...
     * @bodyParam username string required The username for the new account. Example: johnwick
     * @bodyParam email email required The email for the new account. Example: johnwick@gmail.com
     * @bodyParam password string required The password for the new account. Example: abcd1234
     * @bodyParam invitation_code string The invitation code of referral. Example: AASSCC
     * @bodyParam device_type integer required The device type. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createUser( Request $request ) {

        return UserService::createUser( $request );
    }
    
    /**
     * 4. Login an user - Username
     * 
     * <strong>device_type</strong><br>
     * 1: iOS<br>
     * 2: Android<br>
     * 3: Web<br>
     * 
     * @group User API
     * 
     * @bodyParam username string required The username for login. Example: johnwick
     * @bodyParam password string required The password for login. Example: abcd1234
     * @bodyParam device_type integer required The device type. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createToken( Request $request ) {

        return UserService::createToken( $request );
    }

    /**
     * 5. Login an user - Social (Not Using)
     * 
     * <strong>platform</strong><br>
     * 1: Apple<br>
     * 2: Google<br>
     * 
     * <strong>device_type</strong><br>
     * 1: iOS<br>
     * 2: Android<br>
     * 3: Web<br>
     * 
     * @group User API
     * 
     * @bodyParam identifier string required The identifier returned from 3rd party social login API. Example: johnwick@gmail.com
     * @bodyParam uuid string required The uuid return from 3rd party social login API. Example: dasd3-2dcvf-11133
     * @bodyParam platform integer required The 3rd party social login platform. Example: 1
     * @bodyParam device_type integer required The device type. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createTokenSocial( Request $request ) {

        return UserService::createTokenSocial( $request );
    }

    /**
     * 6. Get user
     * 
     * @group User API
     * 
     * @authenticated
     * 
     */ 
    public function getUser( Request $request ) {
        
        return UserService::getUser( $request );
    }
}