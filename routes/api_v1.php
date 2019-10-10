<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CacheApiGetRequests;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('articles', 'ArticleController@index');
Route::get('articles/{id}', 'ArticleController@show');
Route::post('articles', 'ArticleController@store');
Route::put('articles/{id}', 'ArticleController@update');
Route::delete('articles/{id}', 'ArticleController@destroy');

Route::middleware(CacheApiGetRequests::class)->group(function () {
    Route::get('magazines/search', 'MagazineController@index');
    Route::get('publishers/list', 'PublisherController@index');
    Route::get('magazines/{magazine}', 'MagazineController@show');
});
