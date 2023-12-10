<?php
use \Modules\CHTrips\Http\Controllers\Frontend\IndexController;
/*
 * To register a route that needs to be authentication, wrap it in a
 * Route::group() with the auth middleware
 */
Route::group(['middleware' => 'auth'], function() {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('/create', 'IndexController@create')->name('create');
    Route::post('/', 'IndexController@store')->name('store');
    Route::get('/{trip}', [IndexController::class, 'show'])->name('show');
});
