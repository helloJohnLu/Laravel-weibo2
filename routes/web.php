<?php
Route::get('/','StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about','StaticPagesController@about')->name('about');

// 用户注册
Route::get('signup','UsersController@create')->name('signup');
Route::resource('users',    'UsersController');