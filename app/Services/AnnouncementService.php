<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Storage,
    Validator,
};

use App\Models\{
    FileManager,
    UserNotification,
};

use Helper;

use Carbon\Carbon;

class AnnouncementService {

    public static function allAnnouncements( $request ) {

        $notification = UserNotification::select( 'user_notifications.*' );

        $filterObject = self::filter( $request, $notification );
        $notification = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $notification->orderBy( 'user_notifications.created_at', $dir );
                    break;
                case 3:
                    $notification->orderBy( 'user_notifications.type', $dir );
                    break;
                case 4:
                    $notification->orderBy( 'user_notifications.status', $dir );
                    break;
            }
        }

        $notificationCount = $notification->count();

        $limit = $request->length;
        $offset = $request->start;

        $notifications = $notification->skip( $offset )->take( $limit )->get();

        $notifications->append( [
            'encrypted_id',
        ] );

        $notification = UserNotification::select(
            DB::raw( 'COUNT(user_notifications.id) as total'
        ) );

        $filterObject = self::filter( $request, $notification );
        $notification = $filterObject['model'];
        $filter = $filterObject['filter'];

        $notification = $notification->first();

        $totalRecord = 

        $data = [
            'notifications' => $notifications,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $notificationCount : $notification->total,
            'recordsTotal' => $filter ? UserNotification::whereNull( 'user_id' )->count() : $notificationCount,
        ];

        return $data;
    }

    private static function filter( $request, $model ) {

        $model->whereNull( 'user_id' );

        $filter = false;

        if ( !empty( $request->created_date ) ) {
            if ( str_contains( $request->created_date, 'to' ) ) {
                $dates = explode( ' to ', $request->created_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'user_notifications.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->created_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'user_notifications.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if ( !empty( $title = $request->title ) ) {
            $model->where( 'user_notifications.title', 'LIKE', '%' . $request->title . '%' );
            $filter = true;
        }

        if ( !empty( $request->type ) ) {
            $model->where( 'user_notifications.type', $request->type );
            $filter = true;
        }

        if ( !empty( $request->status ) ) {
            $model->where( 'user_notifications.status', $request->status );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneAnnouncement( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $userNotification = UserNotification::find( $request->id );

        if ( $userNotification ) {
            $userNotification->append( [
                'path',
                'display_status'
            ] );
        }

        return $userNotification;
    }

    public static function createAnnouncement( $request ) {

        $validator = Validator::make( $request->all(), [
            'type' => [ 'required', 'in:2,3' ],
            'title' => [ 'required' ],
            'content' => [ 'required' ],
            'image' => [ 'nullable'],
        ] );

        $attributeName = [
            'type' => __( 'datatables.type' ),
            'title' => __( 'datatables.title' ),
            'content' => __( 'announcement.content' ),
            'image' => __( 'announcement.image' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        $createAnnouncementObject = [
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'url_slug' => Str::slug( $request->title ),
            'type' => 2,
        ];

        DB::beginTransaction();

        try {

            $createAnnouncement = UserNotification::create( $createAnnouncementObject );

            $file = FileManager::find( $request->image );
            if ( $file ) {
                $fileName = explode( '/', $file->file );
                $target = 'announcement/' . $createAnnouncement->id . '/' . $fileName[1];
                Storage::disk( 'public' )->move( $file->file, $target );

                $createAnnouncement->image = $target;
                $createAnnouncement->save();

                $file->status = 10;
                $file->save();
            }

            // Check the content for image(s) and mark status 10 in FileManager

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ] );
        }

        return response()->json( [
            'message' => __( 'template.new_x_created', [ 'title' => Str::singular( __( 'template.announcements' ) ) ] ),
        ] );
    }

    public static function updateAnnouncement( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'type' => [ 'required', 'in:2,3' ],
            'title' => [ 'required' ],
            'content' => [ 'required' ],
            'image' => [ 'nullable' ],
        ] );

        $attributeName = [
            'type' => __( 'datatables.type' ),
            'title' => __( 'datatables.title' ),
            'content' => __( 'announcement.content' ),
            'image' => __( 'announcement.image' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        DB::beginTransaction();

        try {

            $updateAnnouncement = UserNotification::find( $request->id );
            $updateAnnouncement->title = $request->title;
            $updateAnnouncement->content = $request->content;

            if ( $request->image ) {
                $file = FileManager::find( $request->image );
                if ( $file ) {

                    Storage::disk( 'public' )->delete( $updateAnnouncement->photo );

                    $fileName = explode( '/', $file->file );
                    $target = 'announcement/' . $updateAnnouncement->id . '/' . $fileName[1];
                    Storage::disk( 'public' )->move( $file->file, $target );
    
                    $updateAnnouncement->image = $target;
                    $updateAnnouncement->save();
    
                    $file->status = 10;
                    $file->save();
                }
            }

            // Check the content for image(s) and mark status 10 in FileManager
            
            $updateAnnouncement->type = $request->type;
            $updateAnnouncement->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ] );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.announcements' ) ) ] ),
        ] );
    }

    public static function updateAnnouncementStatus( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'status' => 'required',
        ] );
        
        $validator->validate();

        try {

            $updateAnnouncement = UserNotification::lockForUpdate()->find( $request->id );
            $updateAnnouncement->status = $request->status;
            $updateAnnouncement->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.announcements' ) ) ] ),
        ] );
    }
}
