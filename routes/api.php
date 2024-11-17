<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\expertcontroller;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('register', 'usercontroller@register');
    Route::post('login', 'usercontroller@login')->name('login');
    Route::post('logout', 'usercontroller@logout');
    Route::post('profile', 'usercontroller@profile');
    Route::post('typeexpert', 'usercontroller@typeexpert');
    Route::post('getexpert', 'usercontroller@getexpert');
    Route::post('consult', 'usercontroller@consult');
    Route::post('allconsult', 'usercontroller@allconsult');
    Route::post('getconsult', 'usercontroller@getconsult');
    Route::post('reservdate', 'usercontroller@reservdate');
    Route::post('deleteconsult', 'usercontroller@deleteconsult');
    Route::post('deletedate', 'usercontroller@deletedate');
    Route::post('alldate', 'usercontroller@alldate');


});

Route::group([

    'middleware' => 'api',
    'prefix' => 'expert'

], function ($router) {
    Route::post('expertregister', 'expertcontroller@expertregister');
    Route::post('expertlogout', 'expertcontroller@expertlogout');
    Route::post('expertprofile', 'expertcontroller@expertprofile');
    Route::post('myconsult', 'expertcontroller@myconsult');
    Route::post('getconsult', 'expertcontroller@getconsult');
    Route::post('answer', 'expertcontroller@answer');
    Route::post('deleteconsult2', 'expertcontroller@deleteconsult2');
    Route::post('deletedate2', 'expertcontroller@deletedate2');
    Route::post('answerdate', 'expertcontroller@answerdate');
    Route::post('mydate', 'expertcontroller@mydate');
    Route::post('getdate', 'expertcontroller@getdate');





});
