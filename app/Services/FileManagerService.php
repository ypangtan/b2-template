<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    DB,
    Validator,
};

use App\Models\{
    FileManager,
};

use Helper;

use Carbon\Carbon;

class FileManagerService
{
    public static function upload( $request ) {

        $createFile = FileManager::create( [
            'file' => $request->file( 'file' )->store( 'file-managers', [ 'disk' => 'public' ] ),
        ] );

        return response()->json( [
            'status' => 200,
            'data' => $createFile,
        ] );
    }

    public static function ckeUpload( $request ) {
     
        $createFile = FileManager::create( [
            'file' => $request->file( 'file' )->store( 'ckeditor', [ 'disk' => 'public' ] ),
        ] );

        return response()->json( [
            'url' => asset( 'storage/' . $createFile->file ),
        ] );
    }
}