<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');


Route::get('/admin/coupons', 'CouponController@show');
Route::post('/coupon', 'CouponController@saveCoupon');
// Registration...
Route::get('/register', 'NewUserController@DisplayUserForm');
Route::post('/register', 'NewUserController@RecordNewuser');
Route::post('/register/select_plan', 'NewUserController@RecordPlan');
Route::post('/register/preferences', 'NewUserController@RecordPlanPreferences');
Route::post('/register/delivery','NewUserController@RecordDeliveryPreferences');
Route::post('/register/payment','NewUserController@RecordPayment');

Route::get('/register/select_plan', array('as' => 'register.select_plan', function() {
	return view('register.select_plan');
}));
Route::get('/register/preferences', array('as' => 'register.preferences', function() {
	return view('register.preferences');
}));
Route::get('/register/delivery', array('as' => 'register.delivery', function() {
	return view('register.delivery');
}));
Route::get('/register/payment', array('as' => 'register.payment', function() {
	return view('register.payment');
}));
Route::get('/congrats', array('as' => 'register.congrats', function() {
	return view('register.congrats');
}));

// Account...
/*Route::get('/account', array('as' => 'account', function() {
	return view('account');
}));*/
// Account...


Route::get('/account/{id?}', 'UserController@getAccount');
Route::post('/account/{id}', 'UserController@editAccount');


// Delivery Schedule...
Route::get('/delivery-schedule','UserController@showDeliverySchedule' );
Route::post('/delivery-schedule', 'UserController@changeDelivery');

Route::get('menu/edit/{id}', array('as' => 'menu.edit', function($id) 
    {
        return View::make('menu-edit') 
            ->with('Menu', Menu::find($id));
    }));

Route::post('menu/edit', function() {
		        //do something
	});
	
Route::post('/menus', 'MenusController@saveMenu');
Route::post('/menufile', 'MenusController@uploadFileToS3');

Route::get('/menus', 'MenusController@showMenus');
Route::get('/menu/{id}', 'MenusController@showMenu');

Route::get('user/new', 'UserController@newUser');
Route::post('user/new', 'UserController@createUser');
Route::post('user/payment/{id}', 'UserController@createUserPayment');
Route::post('user/new/subscription/{id}', 'UserController@createUserSubscription');

Route::get('/users', 'UserController@showUsers');
Route::get('/user/{id}', 'UserController@showUser');
Route::post('/user/{id}', 'UserController@updateUser');
Route::get('/user/payment/{id}', 'UserController@showPayment');
Route::post('/user/payment/{id}', 'UserController@savePayment');

Route::get('/user/subscriptions/{id}', 'UserController@showSubscription');
Route::post('/user/subscriptions/{id}', 'UserController@updateSubscription');
Route::post('/user/csr_note/{id}', 'UserController@saveCSRNote');

Route::get('/user/payments/{id}', 'UserController@showPayments');
Route::get('/referral/subscribe/', 'UserController@recordReferral');


Route::get('/user/referrals/{id}', 'UserController@showReferrals');
Route::post('/user/referrals/{id}', 'UserController@sendReferral');

Route::get('user/test/{id}', 'UserController@showTest');


Route::get('/whatscooking/{date}', 'WhatsCookingsController@showWhatsCookingsDate');	
Route::post('/whatscooking', 'WhatsCookingsController@saveWhatsCooking');
Route::put('/whatscooking', 'WhatsCookingsController@updateWhatsCooking');


Route::get('/admin/whatscooking/{id?}', 'WhatsCookingsController@showWhatsCookings');

Route::get('/admin/dashboard', 'DashboardController@show');
Route::get('/admin/subs_products', 'ProductsController@subscriptionList');
Route::get('/admin/one_time_products', 'ProductsController@oneTimeList');
Route::get('/admin/gift_cards', 'GiftCardsController@show');
Route::get('/admin/subscriptions', 'SubscriptionsController@show');
Route::get('/admin/product_orders', 'OrdersController@show');

Route::post('/stripe/webhook', 'SubinvoiceController@recordStripeInvoice');
Route::get('/admin/services/invoice/test', 'SubinvoiceController@testStripeInvoice');
Route::get('/admin/services/invoice/testjson', 'SubinvoiceController@testStripeJSON');
Route::get('/admin/services/invoice/testshipxml', 'SubinvoiceController@updateShippingStatus');

//ship station routes
Route::get('/shipstation/teststatus','SubinvoiceController@testShippingStatus');
Route::get('/shipstation/getorders','SubinvoiceController@getOrderXML');
Route::post('/shipstation/getorders','SubinvoiceController@updateShippingStatus');

//cancellation, reactivation, hold routes
Route::get('/cancel/{id}', 'SubinvoiceController@CancelSubscription');
Route::get('/cancel/restart/{id}', 'SubinvoiceController@RestartSubscription');
Route::get('/hold/{id}/{holddate}', 'SubinvoiceController@HoldSubscription');
Route::get('/hold/restart/{id}/{holddate}', 'SubinvoiceController@UnHoldSubscription');
Route::get('/hold/check/{id}/{holddate}', 'SubinvoiceController@CheckForHold');
Route::get("/test/date/", 'SubinvoiceController@TestDate');
Route::get('/hold/checkall/', 'SubinvoiceController@CheckHolds');
Route::get('/test/menucontent/', 'SubinvoiceController@testMenus');

//coupon checker
Route::get('/coupon/getamount/{price}/{couponcode}', 'NewUserController@CheckCoupon');

//change plan
Route::get('/plan/childchange/{id}/{numchildren}/{weekof}', 'NewUserController@ChangeRatePlan');

//chron job
Route::get ('/run/scheduled/tasks/', 'CronTasksController@RunTasks');

$router->group(['middleware' => 'admin'], function($router) {
    Route::get('/admin/whatscooking/{id?}', 'WhatsCookingsController@showWhatsCookings');

    Route::get('/admin/users', 'UserController@showUsers');
    Route::get('/admin/user/{id}', 'UserController@showUser');
    Route::post('/admin/user/{id}', 'UserController@updateUser');
    Route::get('/admin/user/payment/{id}', 'UserController@showPayment');
    Route::post('/admin/user/payment/{id}', 'UserController@savePayment');
    Route::get('/admin/user/subscriptions/{id}', 'UserController@showSubscription');
    Route::post('/admin/user/subscriptions/{id}', 'UserController@updateSubscription');
    Route::post('/admin/user/csr_note/{id}', 'UserController@saveCSRNote');
    Route::get('/admin/user/payments/{id}', 'UserController@showPayments');
    Route::get('/admin/referral/subscribe/', 'UserController@recordReferral');
    Route::get('/admin/user/referrals/{id}', 'UserController@showReferrals');
    Route::post('/admin/user/referrals/{id}', 'UserController@sendReferral');

    Route::get('/admin/dashboard', 'DashboardController@show');
    Route::get('/admin/subs_products', 'ProductsController@subscriptionList');
    Route::get('/admin/one_time_products', 'ProductsController@oneTimeList');
    Route::get('/admin/gift_cards', 'GiftCardsController@show');
    Route::get('/admin/subscriptions', 'SubscriptionsController@show');
    Route::get('/admin/product_orders', 'OrdersController@show');
    Route::get('/admin/customers', 'CustomersController@show');
    Route::get('/admin/shipments', 'ShipmentsController@show');
    Route::get('/admin/coupons', 'CouponController@show');
    Route::get('/admin/menu_information', 'MenuInformationController@show');
    Route::get('/admin/recipes', 'RecipesController@showRecipes');
});


Route::get('user/test/{id}', 'UserController@showTest');

