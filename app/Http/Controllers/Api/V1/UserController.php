<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Crypt,
    Hash,
    Http,
    Storage
};

use App\Services\{
    SymbolService,
    UserService,
    WalletService,
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
     * <strong>device_type</strong><br>
     * 1: iOS<br>
     * 2: Android<br>
     * 3: Web<br>
     * 
     * @group User API
     * 
     * @bodyParam username string required The name for the new account. Example: abcd
     * @bodyParam calling_code string required The calling code of the phone. Example: +60
     * @bodyParam phone_number string required The number of the phone. Example: 1234123412
     * @bodyParam email email required The email for the new account. Example: johnwick@gmail.com
     * @bodyParam password string required The password for the new account. Example: abcd1234
     * @bodyParam password_confirmation string required The confirm password for the new account. Example: abcd1234
     * @bodyParam invitation_code string required The invitation code for the new account. Example: abcd1234
     * @bodyParam device_type integer required The device type. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * @bodyParam request_type integer required The request type for OTP. Example: 1
     * 
     */
    public function requestOtp( Request $request ) {

        return UserService::requestOtpApi( $request );
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
     * @bodyParam request_type integer required The request type for OTP. Example: 2
     * 
     */
    public function resendOtp( Request $request ) {

        return UserService::requestOtpApi( $request );
    }

    /**
     * 3. Forgot password
     * 
     * <strong>request_type</strong><br>
     * 3: Forgot Password<br>
     * 
     * @group User API
     * 
     * @bodyParam email email required The email for the new account. Example: johnwick@gmail.com
     * @bodyParam request_type integer required The request type for OTP. Example: 3
     * 
     */
    public function forgotPassword( Request $request ) {

        return UserService::requestOtpApi( $request );
    }

    /**
     * 4. Resend Forgot password
     * 
     * <strong>request_type</strong><br>
     * 4: Resend Forgot Password<br>
     * 
     * @group User API
     * 
     * @bodyParam tmp_user string required The temporary user ID during request OTP. Example: eyJpdiI...
     * @bodyParam request_type integer required The request type for OTP. Example: 4
     * 
     */
    public function resendForgotPassword( Request $request ) {

        return UserService::requestOtpApi( $request );
    }

    /**
     * 5. Create an user
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
     * @bodyParam username string required The name for the new account. Example: abcd
     * @bodyParam calling_code string required The calling code of the phone. Example: +60
     * @bodyParam phone_number string required The number of the phone. Example: 1234123412
     * @bodyParam email email required The email for the new account. Example: johnwick@gmail.com
     * @bodyParam password string required The password for the new account. Example: abcd1234
     * @bodyParam password_confirmation string required The confirm password for the new account. Example: abcd1234
     * @bodyParam security_pin string required The security_pin for the new account. Example: abcd1234
     * @bodyParam security_pin_confirmation string required The confirm security_pin for the new account. Example: abcd1234
     * @bodyParam invitation_code string required The invitation code for the new account. Example: abcd1234
     * @bodyParam device_type integer required The device type. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createUser( Request $request ) {

        return UserService::createUser( $request );
    }
    
    /**
     * 6. Login an user
     * 
     * <strong>device_type</strong><br>
     * 1: iOS<br>
     * 2: Android<br>
     * 3: Web<br>
     * 
     * @group User API
     *      
     * @bodyParam email email required The email for login. Example: johnwick
     * @bodyParam password string required The password for login. Example: abcd1234
     * @bodyParam device_type integer required The device type. Example: 1
     * @bodyParam register_token string The device token to receive notification. Example: example_device_token
     * 
     */
    public function createToken( Request $request ) {

        return UserService::createToken( $request );
    }

    /**
     * 7. verify Forgot Password
     * 
     * <strong>request_type</strong><br>
     * 3: Forgot Password<br>
     * 
     * @group User API
     * 
     * @bodyParam identifier string required The temporary user ID during request OTP. Example: eyJpdiI...
     * @bodyParam request_type integer required The request type for OTP. Example: 3
     * @bodyParam otp_code integer required The OTP code to verify register. Example: 487940
     * 
     */
    public function verifyForgotPassword( Request $request ) {

        return UserService::verifyOTP( $request );
    }

    /**
     * 8. Reset Password
     * 
     * @group User API
     * 
     * @bodyParam identifier string required The temporary user ID during request OTP. Example: eyJpdiI...
     * @bodyParam password string required The password for the new account. Example: abcd1234
     * @bodyParam password_confirmation string required The repeat new password of the user. Example: abcd1234
     * 
     */
    public function resetPassword( Request $request ) {

        return UserService::resetPassword( $request );
    }

    /**
     * 9. Get user
     * 
     * @group User API
     * 
     * @authenticated
     * 
     */ 
    public function getUser( Request $request ) {
        
        return UserService::getUser( $request );
    }
    
    /**
     * 10. Get User KYC Status
     * 
     * @group User API
     * 
     * @authenticated
     * 
     */
    public function kycStatus(){
        return Helper::kycStatus();
    }
    
    /**
     * 11. Get User Wallet Balance
     * 
     * @group User API
     * 
     * @authenticated
     * 
     */
    public function walletInfos(){
        return Helper::walletInfos();
    }
    
    /**
     * 12. All User
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam user string required The email of the user. Example: abcd1234
     * 
     */
    public function allUsers( Request $request ){
        return UserService::_allUsers( $request );
    }

    /**
     * 13. Update User Password
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam old_password string required The old password. Example: 1
     * @bodyParam password string required The new password. Example: 1
     * @bodyParam password_confirmation string required The confirm password. Example: 1
     * 
     */
    public function updateUserPassword( Request $request ){
        return UserService::updateUserPassword( $request );
    }

    /**
     * 14. Update or Create User Security Pin
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam old_security_pin string required The old security pin(nullable when user dont have old security pin). Example: 1
     * @bodyParam security_pin string required The new security pin. Example: 1
     * @bodyParam security_pin_confirmation string required The confirm security pin. Example: 1
     * 
     */
    public function updateSecurityPin( Request $request ){
        return UserService::updateSecurityPin( $request );
    }

    /**
     * 15. Setup My Team
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam id string nullable The encrypted id of user (if null use auth()->user()->id ). Example: 1
     * 
     */
    public function initMyTeam( Request $request ){
        return UserService::initMyTeam( $request );
    }

    /**
     * 16. My Team Ajax
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam id string nullable The encrypted id of user. Example: 1
     * 
     */
    public function myTeamAjax( Request $request ){
        return UserService::myTeamAjax( $request );
    }

    /**
     * 17. Update User Photo
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam photo file required The photo for user.
     * 
     */
    public function updateUserPhoto( Request $request ){
        return UserService::updateUserPhoto( $request );
    }

    /**
     * 18. Search Downlines
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam user string required The name or email of user.Example: abcd@gmail.com
     * 
     */
    public function allDownlines( Request $request ){
        return UserService::allDownlines( $request );
    }

    /**
     * 19. Search User in My team
     * 
     * @group User API
     * 
     * @authenticated
     * 
     * @bodyParam user string required The name or email of user.Example: abcd@gmail.com
     * 
     */
    public function searchMyTeam( Request $request ){
        return UserService::searchMyTeam( $request );
    }
}