<?php
use Intervention\Image\Facades\Image as Img;

Route::get('/', 'HomeController@index')->name('index');

Route::get('api/lang.js', function () {
    $files = glob(resource_path('lang/en/*.php'));
    $strings = [];

    foreach ($files as $file) {
        $name = basename($file, '.php');
        $strings[$name] = require $file;
    }

    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');

// Accessible additional Routes
Route::get('/profile/default/avatar', function () {
    return Img::make(storage_path().'/uploads/defaults/default-avatar.png')->response();
})->name('front.default.avatar');
Route::get('/profile/avatar/{user_id}/{filename}', function ($user_id, $filename) {
    $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';
    return Img::make($storage_base . 'users/'.$user_id.'/avatars/'.$filename)->response();
})->name('front.get.avatar');
// Fake Post Images
Route::get('/post/images/default/{filename}', function ($filename) {
    return Img::make(storage_path().'/uploads/defaults/post_images/'.$filename)->response();
})->name('front.default.post_image');

// Payment routes
Route::post('/create_donation', 'PaymentController@createDonation');
Route::get('/ipn', 'PaymentController@ipn')->name('ipn');
Route::get('/capture', 'PaymentController@captureAuthorization')->name('capture');
Route::post('/create_verification', 'PaymentController@createVerification')->name('paypal.verify');
Route::get('/verify_paypal', 'PaymentController@createVerifyAuthorization')->name('paypal.authorize');
Route::post('/create_payout', 'PaymentController@createPayout')->name('paypal.payout');

// AWS Auth URL for Media Editor
Route::get('/aws_sign_key', 'MediaEditorController@getAWSSignKey')
    ->name('videos.aws_sign_key');

Route::get('/password/reset/{token}', 'HomeController@welcome')->name('password.reset');

Route::post('/paypal_notifications', 'PaymentController@handlePaypalNotification');

Route::get('/let_me_in_please', function() {
    $cookie = Cookie::make('dev_access_digitalidea', 'yeah', 60 * 24 * 365);
    return redirect('/')->withCookie($cookie);
});

Horizon::auth(function ($request) {
    return in_array(auth()->user()->email, [
        'admin@actionlime.com',
    ]);
});
