<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get( '/', function () {
    return redirect( '/backoffice/dashboard' );
} );

Route::get( 'home', function () {
    return redirect( '/backoffice/dashboard' );
} );

require __DIR__ . '/admin.php';
require __DIR__ . '/branch.php';