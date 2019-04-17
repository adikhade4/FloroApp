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

Route::post('login', 'API\UserController@login');



// Route::get('logout', 'API\UserController@logout');


 Route::group(['middleware' => 'auth:api'], function(){
 Route::get('users', 'API\UserController@index');
Route::post('details', 'API\UserController@details');
Route::post('create','API\UserController@store');

Route::get('logout', 'API\UserController@logout');

Route::post('search','API\UserController@getSearchResults');
Route::post('sort','API\UserController@sortUsers');

Route::post('update/{id}','API\UserController@update');
Route::delete('user/{id}','API\UserController@delete');

Route::get('/export/users', 'ExportUserController@exportUsers');
Route::get('/download/users', 'ExportUserController@showUsersDownload')->name('showUsersDownload');
Route::get('/download/users-file', 'ExportUserController@downloadUsers')->name('usersDownload');


});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
   
// });
