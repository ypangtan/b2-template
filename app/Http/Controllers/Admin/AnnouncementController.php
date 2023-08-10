<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    AnnouncementService,
    FileManagerService,
};

class AnnouncementController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.announcements' );
        $this->data['content'] = 'admin.announcement.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.announcements' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.announcements' ),
        ];

        return view( 'admin.main' )->with( $this->data );   
    }

    public function add() {

        $this->data['header']['title'] = __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.announcements' ) ) ] );
        $this->data['content'] = 'admin.announcement.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.announcements' ),
            'title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.announcements' ) ) ] ),
            'mobile_title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.announcements' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );  
    }

    public function edit( Request $request ) {

        $this->data['header']['title'] = __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.announcements' ) ) ] );
        $this->data['content'] = 'admin.announcement.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.announcements' ),
            'title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.announcements' ) ) ] ),
            'mobile_title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.announcements' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );  
    }

    public function allAnnouncements( Request $request ) {
        return AnnouncementService::allAnnouncements( $request );
    }

    public function oneAnnouncement( Request $request ) {
        return AnnouncementService::oneAnnouncement( $request );
    }

    public function createAnnouncement( Request $request ) {
        return AnnouncementService::createAnnouncement( $request );
    }

    public function updateAnnouncement( Request $request ) {
        return AnnouncementService::updateAnnouncement( $request );
    }

    public function updateAnnouncementStatus( Request $request ) {
        return AnnouncementService::updateAnnouncementStatus( $request );
    }

    public function ckeUpload( Request $request ) {
        return FileManagerService::ckeUpload( $request );
    }
}
