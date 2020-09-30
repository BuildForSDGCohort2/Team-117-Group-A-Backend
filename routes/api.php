<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => ['api', 'multiauth:api,companies']], function () {
    Route::get('/user',  'AuthController@user');
    Route::get('/logout', 'AuthController@logout');
});
//newly created middleware provider (at config/auth.php)
// Route::post('/accept', 'AuthController@allow')->middleware('multiauth:companies');
Route::post('register', 'AuthController@register');
Route::post('registerCompany', 'AuthController@registerCompany');
Route::group(['middleware' => ['api', 'multiauth:companies']], function () {
    // Route::get('/companies', function (Request $request) {
    //     // The instance of user authenticated (Admin or User in this case) will be returned
    //     return $request->user();
    // });
});

//Tests
Route::post('/addTest', 'TestController@add');
Route::get('/tests', 'TestController@all');
Route::delete('/test/{id}', 'TestController@delete');
Route::put('/test/{id}', 'TestController@update');

//Requests
Route::post('/addRequest', 'RequestController@add');
Route::get('/requests', 'RequestController@all');
Route::delete('/request/{id}', 'RequestController@delete');
Route::put('/request/{id}', 'RequestController@update');