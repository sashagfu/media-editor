<?php

Route::get('/', ['as' => 'admin.welcome', 'uses'=> 'AdminController@index']);
Route::resource('users', 'UserController');
Route::resource('videos', 'VideoController');
Route::resource('posts', 'PostController');
Route::resource('flags', 'FlagController');
Route::resource('flag_reasons', 'FlagReasonController');
Route::resource('comments', 'CommentController');
Route::get('reports/users', 'ReportController@users');
Route::patch('flags/{flag}/verify', 'FlagController@verify')->name('flags.verify');
Route::get('posts/{post}/flags', 'FlagController@index')->name('posts.flags');
Route::get('posts/{post}/comments', 'CommentController@index')->name('posts.comments');
Route::get('posts/{post}/likes', 'UserReactionController@likes')->name('posts.likes');
Route::get('posts/{post}/stars', 'UserReactionController@stars')->name('posts.stars');
