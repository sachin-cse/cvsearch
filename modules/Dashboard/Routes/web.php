<?php
use \Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'user','middleware' => ['auth','verified']],function(){
    Route::get('/dashboard','DashboardController@index')->name("user.dashboard");
});
