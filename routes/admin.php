<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'guest', 'namespace' => 'General'], function () {
    Route::post('login', 'GeneralController@login')->name('login_post');
    Route::get('login', 'GeneralController@Panel_Login')->name('login');
    Route::get('forgot_password', 'GeneralController@Panel_Pass_Forget')->name('forgot_password');
    Route::post('forgot_password', 'GeneralController@ForgetPassword')->name('forgot_password_post');


});

Route::group(['middleware' => 'Is_Admin'], function () {
    Route::get('/', 'General\GeneralController@Admin_dashboard')->name('dashboard');
    Route::get('/totalusers', 'General\GeneralController@totalusers')->name('totalusers');
    Route::get('/profile', 'General\GeneralController@get_profile')->name('profile');
    Route::post('/profile', 'General\GeneralController@post_profile')->name('post_profile');
    Route::get('/update_password', 'General\GeneralController@get_update_password')->name('get_update_password');
    Route::post('/update_password', 'General\GeneralController@update_password')->name('update_password');
    Route::get('/site_settings', 'General\GeneralController@get_site_settings')->name('get_site_settings');
    Route::post('/site_settings', 'General\GeneralController@site_settings')->name('site_settings');
    Route::group(['namespace' => 'Admin'], function () {
        //        User Module
        Route::get('user/listing', 'UsersController@listing')->name('user.listing');
        Route::get('user/status_update/{id}', 'UsersController@status_update')->name('user.status_update');
        Route::resource('user', 'UsersController')->except(['create', 'store']);

        //Content Module
        // Route::resource('content', 'ContentController')->except(['show', 'create', 'store', 'destroy']);
        // Route::get('content/listing', 'ContentController@listing')->name('content.listing');
   
        // Category Controller
        Route::get('user/status_update/{id}', 'CategoryController@status_update')->name('category.status_update');
        Route::get('category/listing', 'CategoryController@listing')->name('category.listing');
        Route::resource('category', 'CategoryController');

         // Role Controller
         Route::get('role/status_update/{id}', 'RoleController@status_update')->name('role.status_update');
         Route::get('role/listing', 'RoleController@listing')->name('role.listing');
         Route::resource('role', 'RoleController');
         //Branch Controller
         Route::get('product/status_update/{id}', 'ProductController@status_update')->name('product.status_update');
         Route::get('product/listing', 'ProductController@listing')->name('product.listing');
         Route::resource('product', 'ProductController');
 
         //Branch Controller
         Route::get('branch/status_update/{id}', 'BranchController@status_update')->name('branch.status_update');
         Route::get('branch/listing', 'BranchController@listing')->name('branch.listing');
         Route::resource('branch', 'BranchController');
 
        //Color Controller
        Route::get('color/status_update/{id}', 'ColorController@status_update')->name('color.status_update');
        Route::get('color/listing', 'ColorController@listing')->name('color.listing');
        Route::resource('color', 'ColorController');

    });
});
