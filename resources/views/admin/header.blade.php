<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ Helper::websiteName() }} Admin Dashboard">
    <meta name="author" content="BaseTwo">
    <meta name="keywords" content="BaseTwo Admin">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="robots" content="noindex">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="{{ asset( 'favicon.ico' ) . Helper::assetVersion() }}" />
    <link rel="apple-touch-icon" href="{{ asset( 'admin/img/icons/default.png' ) . Helper::assetVersion() }}" />
    @if( 1 == 2 ) 
    <link href="{{ asset( 'admin/splashscreens/iphone5_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/iphone6_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/iphoneplus_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/iphonex_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/iphonexr_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/iphonexsmax_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/ipad_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/ipadpro1_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/ipadpro3_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset( 'admin/splashscreens/ipadpro2_splash.png' ) . Helper::assetVersion() }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    
    <link rel="manifest" href="{{ asset( 'manifest.json' ) . Helper::assetVersion() }}" />
    <script>
        window.addEventListener( 'load', () => {
            registerSW();
        });
    
        // Register the Service Worker
        async function registerSW() {
            if( 'serviceWorker' in navigator ) {
                try {
                    await navigator
                    .serviceWorker
                    .register( '{{ asset( 'serviceworker.js' ) . Helper::assetVersion() }}' );
                }
                catch (e) {
                    console.log( 'SW registration failed' );
                }
            }
        }
    </script>
    @endif
 
    @if( @$header )
    <title>{{ @$header['title'] }} - {{ Helper::websiteName() }}</title>
    @else
    <title>{{ Helper::websiteName() }} Admin Panel</title>
    @endif

    <link href="{{ asset( 'admin/css/bootstrap.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/dataTables.bootstrap5.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/app.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/custom.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/font.css' ) . Helper::assetVersion() }}" rel="stylesheet">

    <link href="{{ asset( 'admin/css/template_extended.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/flatpickr.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/choices.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/lightgallery.css' ) . Helper::assetVersion() }}" rel="stylesheet">
    <link href="{{ asset( 'admin/css/image-uploader.min.css' ) . Helper::assetVersion() }}" rel="stylesheet">
</head>

<body>