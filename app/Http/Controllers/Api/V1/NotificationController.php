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

class NotificationController extends Controller {

    /**
     * 1. All Notification
     * 
     * @group Notification API
     * 
     * @authenticated
     * 
     */
    public function allNotification(){
        return \Helper::userNotifications();
    }
    
    /**
     * 2. One Notification
     * 
     * @group Notification API
     * 
     * @authenticated
     * @bodyParam id string required The id of the notification. Example: 1
     * 
     */
    public function oneNotification( Request $request ){
        return UserService::oneUserNotification( $request );
    }

    /**
     * 3. All Read Notification
     * 
     * @group Notification API
     * 
     * @authenticated
     * 
     */
    public function allReadNotification( Request $request ){
        return UserService::allReadNotification( $request );
    }
}