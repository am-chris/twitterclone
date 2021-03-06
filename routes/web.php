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

Auth::routes();

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::group(['middleware' => 'auth', 'prefix' => 'api'], function () {
    Route::post('u/{id}/cover_photo', 'User\CoverPhotoController@store')->name('api.users.cover_photos.store');
    Route::delete('u/{id}/cover_photo', 'User\CoverPhotoController@destroy')->name('api.users.cover_photos.destroy');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('notifications', 'NotificationController@index')->name('notifications.index');

    Route::post('p/{post}/like', 'Post\LikeController@store')->name('posts.likes.store');
    Route::delete('p/{post}/unlike', 'Post\LikeController@destroy')->name('posts.likes.destroy');

    Route::post('p/{post}/share', 'Post\ShareController@store')->name('posts.shares.store');
    Route::delete('p/{post}/unshare', 'Post\ShareController@destroy')->name('posts.shares.destroy');

    Route::post('u/{user}/block', 'User\BlockController@store')->name('users.blocks.store');
    Route::post('u/{user}/unblock', 'User\BlockController@destroy')->name('users.blocks.destroy');

    Route::post('u/{user}/follow', 'User\FollowController@store')->name('users.follows.store');
    Route::delete('u/{user}/unfollow', 'User\FollowController@destroy')->name('users.follows.destroy');
    Route::post('u/{user}/approve_follow_request', 'User\FollowController@approve_follow_request')->name('users.follow_requests.approve');
    Route::post('u/{user}/deny_follow_request', 'User\FollowController@deny_follow_request')->name('users.follow_requests.deny');

    Route::post('u/{user}/photo', 'User\PhotoController@store')->name('users.photos.store');
    Route::delete('u/{user}/photo', 'User\PhotoController@destroy')->name('users.photos.destroy');

    Route::post('u/{user}/report', 'User\ReportController@store')->name('users.reports.store');

    Route::put('users/{user}', 'UserController@update')->name('users.update');

    Route::get('settings', 'SettingController@index')->name('settings.index');
    Route::put('settings/{user}/password', 'Setting\PasswordController@update')->name('settings.passwords.update');
});

Route::group(['middleware' => ['role:admin', 'auth'], 'prefix' => 'admin'], function () {
    Route::get('/', 'AdminController@index')->name('admin.index');
});

Route::resource('posts', 'PostController');

Route::get('{username}', 'UserController@show')->name('users.show');
Route::get('{username}/edit', 'UserController@edit');
Route::get('{username}/followers', 'UserController@followers');
Route::get('{username}/following', 'UserController@following');

Route::get('hashtag/{hashtag}', 'HashtagController@index')->name('hashtags.index');

Route::get('api/{user_id}/timeline', 'Api\TimelineController@index')->name('api.timeline');
Route::get('api/{user_id}/individual_timeline', 'Api\TimelineController@individual')->name('api.user_timeline');
Route::get('api/{user_id}/p/liked', 'Api\Post\LikeController@index');
