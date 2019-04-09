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
    return redirect('/users');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');


    // Routes for user management.
    Route::resource('users', 'UserController');
   
    
    // Routes for export & download users.
    Route::get('/export/users', 'ExportUserController@exportUsers')->name('usersExport');
    Route::get('/download/users', 'ExportUserController@showUsersDownload')->name('showUsersDownload');
    Route::get('/download/users-file', 'ExportUserController@downloadUsers')->name('usersDownload');