<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="{{ asset( 'favicon.ico' ) }}" type="image/png" />
        <!--plugins-->
        <link href="{{ asset( 'admin/plugins/vectormap/jquery-jvectormap-2.0.2.css' ) .Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/plugins/simplebar/css/simplebar.css' ) .Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/plugins/perfect-scrollbar/css/perfect-scrollbar.css' ) .Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/plugins/metismenu/css/metisMenu.min.css' ) .Helper::assetVersion() }}" rel="stylesheet" />
        <!-- Bootstrap CSS -->
        <link href="{{ asset( 'admin/css/bootstrap.min.css' ) . Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/css/bootstrap-extended.css' ) . Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/css/dataTables.bootstrap5.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">
        <link href="{{ asset( 'admin/css/style.css' ) . Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/css/custom.css' ) . Helper::assetVersion() }}" rel="stylesheet" />
        <!-- <link href="{{ asset( 'admin/css/icons.css' ) . Helper::assetVersion() }}" rel="stylesheet" /> -->
        <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" /> -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <link href="{{ asset( 'admin/css/lightgallery.min.css' ) . Helper::assetVersion() }}" rel="stylesheet" />
        <!-- loader-->
        <link href="{{ asset( 'admin/css/pace.min.css' ) . Helper::assetVersion() }}" rel="stylesheet" />
        <link href="{{ asset( 'admin/css/flatpickr.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">
        <link href="{{ asset( 'admin/css/image-uploader.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">

        @if ( @$header )
            <title>{{ @$header['title'] }} - {{ Helper::websiteName() }}</title>
        @else
            <title>{{ Helper::websiteName() }} Admin Panel</title>
        @endif
    </head>