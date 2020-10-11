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
    Route::get('/userRequests', 'RequestController@user');
});
//newly created middleware provider (at config/auth.php)
// Route::post('/accept', 'AuthController@allow')->middleware('multiauth:companies');
Route::post('register', 'AuthController@register');
Route::post('registerCompany', 'AuthController@registerCompany');

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

// Accepted
Route::post('/addAccepted', 'AcceptedController@add');
Route::get('/accepted', 'AcceptedController@all');
Route::get('/companyAccepted/{id}', 'AcceptedController@companyAll');
Route::delete('/accepted/{id}', 'AcceptedController@delete');

//Results
Route::post('/addResult', 'ResultController@add');
Route::get('/results', 'ResultController@all');
Route::delete('/result/{id}', 'ResultController@delete');
Route::put('/result/{id}', 'ResultController@update');
