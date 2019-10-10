<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-factory', 'DebugController@testFactory');

// globalny constraint w app/Providers/RouteServiceProvider
Route::get('test-id/{id}', function($id) {
    exit('ok');
});

Route::view('test-middleware', 'welcome')->middleware('checkAge');
Route::view('test-middleware-2', 'welcome')
    ->middleware('\App\Http\Middleware\CheckTime')
;

Route::view('test-middleware-3', 'welcome')
    ->middleware('App\Http\Middleware\CheckSomething')
;

Route::get('test-kolejnosci-middleware', 'DebugController@middlewarePriorities')
    ->middleware('App\Http\Middleware\CacheApiGets')
;
