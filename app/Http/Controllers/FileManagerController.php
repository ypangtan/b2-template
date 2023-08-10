<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\{
    FileManagerService,
};

class FileManagerController extends Controller
{
    public function upload( Request $request ) {

        return FileManagerService::upload( $request );
    }

    public function ckeUpload( Request $request ) {

        return FileManagerService::ckeUpload( $request );
    }
}
