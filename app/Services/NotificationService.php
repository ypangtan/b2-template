<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Crypt,
    DB,
    Hash,
    Http,
    Storage,
    Validator,
};
use App\Models\{
    ApiLog,
    Country,
    OtpAction,
    TmpUser,
    User,
    UserDetail,
    UserDevice,
    UserNotification,
    UserNotificationSeen,
    UserSocial,
    UserStructure,
    UserWallet,
};

use Illuminate\Validation\Rules\Password;

use App\Rules\CheckASCIICharacter;

use Helper;

use Carbon\Carbon;

class NotificationService {

    public static function userNotifications() {
        $notifications = UserNotification::select( 
            'user_notifications.*',
            \DB::raw( '( SELECT COUNT(*) FROM user_notification_seens AS a WHERE a.user_notification_id = user_notifications.id AND a.user_id = ' .auth()->user()->id. ' ) as is_read' )
        )->orderBy( 'user_notifications.created_at', 'DESC' )->get();
        
        $totalUnread = UserNotificationSeen::where( 'user_id', auth()->user()->id )->count();
        
        $data['total_unread'] = count( $notifications ) - $totalUnread;
        $data['notifications'] = $notifications;
        
        return $data;
    }

    public static function oneUserNotification( $request ) {

        $notifications = UserNotification::find( $request->id );

        $seen = UserNotificationSeen::updateOrCreate( [
            'user_notification_id' => $request->id,
            'user_id' => auth()->user()->id
        ] );
        
        $data['notifications'] = $notifications;
        
        return $data;
    }

    public static function allReadNotification( $request ) {
        
        try {
            \DB::beginTransaction();

            $notifications = UserNotification::with( [ 'UserNotificationSeens' ] )
                ->where( 'status', 10 )
                ->whereNull( 'user_id' )
                ->whereDoesntHave( 'UserNotificationSeens' )
                ->orderBy( 'created_at', 'DESC' )
                ->get();
            
            foreach( $notifications as $notification ){
                $seen = UserNotificationSeen::create( [
                    'user_notification_id' => $notification->id,
                    'user_id' => auth()->user()->id
                ] );
            }

            \DB::commit();
            
            return response()->json( [
                'data' => 'success'
            ] );
        
        } catch ( \Throwable $th ) {
        
            \DB::rollBack();
            abort( 500, $th->getMessage() . ' in line: ' . $th->getLine() );
        }
    }
}