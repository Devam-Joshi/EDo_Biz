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
         
         //Product Controller
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

        //Account Controller
        Route::get('account/status_update/{id}', 'AccountController@status_update')->name('account.status_update');
        Route::match(['get','post'],'account/listing', 'AccountController@listing')->name('account.listing');
        Route::resource('account', 'AccountController');

        //Account-Group Controller
        Route::get('account-group/status_update/{id}', 'AccountGroupController@status_update')->name('account-group.status_update');
        Route::get('account-group/listing', 'AccountGroupController@listing')->name('account-group.listing');
        Route::resource('account-group', 'AccountGroupController');

        // City Controller
        Route::get('city/status_update/{id}', 'CityController@status_update')->name('city.status_update');
        Route::get('city/listing', 'CityController@listing')->name('city.listing');
        Route::resource('city', 'CityController');


        //====Inquery ======================
        Route::resource('inquiry-new', 'InquiryNewController');
        Route::get('newinquiry-to-inquiry/{id}','InqueryController@convertToInquiry');
        Route::post('inquery-conversion-store/{id}','InqueryController@convertInquirySave')->name('inquery-conversion-store');
        Route::resource('inquiry', 'InqueryController');
        Route::resource('sale-order', 'SaleOrderController');
        Route::get('convert-inquiry-saleorder/{id}','SaleOrderController@convertInqueryToOrder');
        Route::resource('sale', 'SaleController');
        Route::get('saleorder-to-salebill/{id}','SaleController@convertOrderToBill');
        Route::get('saleorder-to-salebill-ready/{id}','SaleController@convertOrderToReadyBill');
        Route::resource('purchase-order', 'PurchaseOrderController');
        Route::resource('purchase', 'PurchaseController');
        //====Payment/ Financial Routes=====
        Route::group(['prefix'=>'payment','as'=>'payment.'], function(){
            //====Common Payment ========
            Route::resource('/', 'PaymentController');
            Route::match(['get','post'],'search', 'PaymentController@index');

            //====Inward Payment =========
            Route::resource('inward', 'PaymentInwardController');
            Route::match(['get','post'],'inward/listing', 'PaymentInwardController@listing')->name('inward.listing');

            //====Outward Payment =========
            Route::resource('outward', 'PaymentOutwardController');
            Route::match(['get','post'],'outward/listing', 'PaymentOutwardController@index')->name('outward.listing');

            //=== Payment Transfer ==========
            Route::resource('transfer', 'PaymentTransferController');
            Route::match(['get','post'],'search', 'PaymentTransferController@index')->name('transfer.listing');

        });

        //======Qr Code Not Generate ===          
        Route::get('generate_stock_qrcode','AjaxController@GenerateQrString')->name('generate_stock_qrcode');

        //===Reports=====
        Route::get('client-product-association','AccountController@listing')->name('client-product-association');
        Route::get('partywise-overdue-bills','AccountController@listing')->name('partywise-overdue-bills');
        
        //====AJAX  url=======
        Route::get('getAccountList/{group}/{name}','AjaxController@AccountList');
        Route::get('getAccountDetail/{id}','AjaxController@AccountDetail');
        Route::get('getLastprice/{type}/{partyID?}/{itemids?}','AjaxController@lastItemSalePrice');
        Route::get('getAccountPrevPayment/{type}/{partyID?}/{itemids?}','AjaxController@accPrevPayment');
        Route::get('search-account-newinquery/{group}/{keyword}','AjaxController@AccNewInqSearch');

        //=== Ajax Product Search ==
        Route::get('search-prod-name/{name}','AjaxController@searchProdName');
        Route::get('search-prod-qr/{qrcode}/{catall?}','AjaxController@searchProdQrCode');
        Route::get('product-all-active-variants/{id}/{catid}','AjaxController@searchProdVariants');


        //=====Other Action====
        Route::get('action/{action}/{id?}','AjaxController@multiAction');
        
    });
});
