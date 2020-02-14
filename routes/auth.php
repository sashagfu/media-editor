<?php

Route::patch('/users/{user_id}/verify', 'Auth\RegisterController@verifyCheck')->name('auth.verify_check');

Route::get('/unregistered_invite/{encoded_invite}', 'Auth\RegisterController@circleInvite')
    ->name('unregistered.invite');

// Reset password
Route::get('/password/forget', 'Auth\ForgotPasswordController@index');
Route::post('/password/forget', 'Auth\ForgotPasswordController@sendResetLinkEmail');

Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
