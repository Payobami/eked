<?php

use Illuminate\Http\Request;

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

Route::post('/myshape', 'ShapeController@myshape');

//User API Controller
Route::post('/create','APIAuthController@cdata');
Route::post('/register','APIAuthController@register');
Route::post('/login','APIAuthController@login');
Route::post('/complete_profile','APIAuthController@complete_profile');
Route::get('/user','APIAuthController@getCurrentUser');
Route::post('/update','APIAuthController@update');
Route::get('/logout','APIAuthController@logout');




