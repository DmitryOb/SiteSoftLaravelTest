<?php

Route::get('/', function () {
	return view('index');
});

Route::get('/login', function(){ 
	return view('login');
});

Route::get('/reg', function () {
	return view('register');
});

Route::post('/store', 'UserController@store');
Route::post('/login', 'UserController@logs');
Route::get('/logout', 'UserController@logout');
Route::post('/ajaxcheck', 'UserController@ajaxcheck');
Route::post('/ajaxcheckPrivate', 'UserController@ajaxcheckPrivate');