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
        <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
        <!-- <link href="https://fonts.cdnfonts.com/css/montserrat" rel="stylesheet"> -->
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />

        <style>
        /* montserrat-regular - latin */
        @font-face {
        font-display: swap; /* Check https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display for other options. */
        font-family: 'Montserrat';
        font-style: normal;
        font-weight: 400;
        src: url('{{ asset( 'admin/font/montserrat-v25-latin-regular.eot' ) }}'); /* IE9 Compat Modes */
        src: url('{{ asset( 'admin/font/montserrat-v25-latin-regular.eot?#iefix' ) }}') format('embedded-opentype'), /* IE6-IE8 */
            url('{{ asset( 'admin/font/montserrat-v25-latin-regular.woff2' ) }}') format('woff2'), /* Super Modern Browsers */
            url('{{ asset( 'admin/font/montserrat-v25-latin-regular.woff' ) }}') format('woff'), /* Modern Browsers */
            url('{{ asset( 'admin/font/montserrat-v25-latin-regular.ttf' ) }}') format('truetype'), /* Safari, Android, iOS */
            url('{{ asset( 'admin/font/montserrat-v25-latin-regular.svg#Montserrat' ) }}') format('svg'); /* Legacy iOS */
        }
        @font-face {
        font-display: swap; /* Check https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display for other options. */
        font-family: 'Montserrat';
        font-style: normal;
        font-weight: 600;
        src: url('{{ asset( 'admin/font/montserrat-v25-latin-600.eot' ) }}'); /* IE9 Compat Modes */
        src: url('{{ asset( 'admin/font/montserrat-v25-latin-600.eot?#iefix' ) }}') format('embedded-opentype'), /* IE6-IE8 */
            url('{{ asset( 'admin/font/montserrat-v25-latin-600.woff2' ) }}') format('woff2'), /* Super Modern Browsers */
            url('{{ asset( 'admin/font/montserrat-v25-latin-600.woff' ) }}') format('woff'), /* Modern Browsers */
            url('{{ asset( 'admin/font/montserrat-v25-latin-600.ttf' ) }}') format('truetype'), /* Safari, Android, iOS */
            url('{{ asset( 'admin/font/montserrat-v25-latin-600.svg#Montserrat' ) }}') format('svg'); /* Legacy iOS */
        }
        </style>

        @if ( @$header )
            <title>{{ @$header['title'] }} - {{ Helper::websiteName() }}</title>
        @else
            <title>Backoffice - {{ Helper::websiteName() }}</title>
        @endif
    </head>