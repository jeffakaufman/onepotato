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
Route::get('/join', 'NewUserController@DisplayUserForm');
Route::get('/register', 'NewUserController@DisplayUserForm');
Route::get('/register/{referralId}', 'NewUserController@DisplayUserForm');
Route::post('/register', 'NewUserController@RecordNewuser');
Route::post('/register/select_plan', 'NewUserController@RecordPlan');
Route::post('/register/preferences', 'NewUserController@RecordPlanPreferences');
Route::post('/register/delivery','NewUserController@RecordDeliveryPreferences');
Route::post('/register/payment','NewUserController@RecordPayment');
Route::post('/register/waiting_list','NewUserController@SubscribeToWaitingList');

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

// Static...
Route::get('/faq', array('as' => 'faq', function() {
	return view('static.faq');
}));
Route::get('/how-it-works', array('as' => 'static.how_it_works', function() {
    return view('static.how_it_works');
}));
Route::get('/learn-more', array('as' => 'static.learn_more', function() {
    return view('static.learn_more');
}));
Route::get('/about-us', array('as' => 'static.about_us', function() {
    return view('static.about_us');
}));
Route::get('/recycling', array('as' => 'static.recycling', function() {
    return view('static.recycling');
}));
Route::get('/handling', array('as' => 'static.handling', function() {
    return view('static.handling');
}));

// Account...
//Route::get('/account/{id?}', array('middleware' => 'auth', 'uses' => 'UserController@getAccount'));
Route::get('/account/{tab?}', array('middleware' => 'auth', 'uses' => 'UserController@getAccount'));
Route::post('/account/{id}', 'UserController@editAccount');

Route::get('/account/cancel/{code}', array('as' => 'cancel.account.link', 'middleware' => 'auth', 'uses' => 'UserController@ResolveCancelLink', ))->where('code', '(.*)');


Route::post('/accountcancel', [
    'middleware' => 'auth',
    'uses' => 'UserController@cancelUserAccount'
]);

Route::post('/account_reactivate', [
    'middleware' => 'auth',
    'uses' => 'UserController@reactivateUserAccount'
]);

Route::get('/whats-cooking', array('as' => 'whats_cooking', function() {
    return view('whats_cooking')->with(['defaultWeek' => false]);
}));

Route::get('/whats-cooking/{date}', array('as' => 'whats_cooking', function($date) {
    return view('whats_cooking')->with(['defaultWeek' => $date]);
}));

// Delivery Schedule...
Route::get('delivery-schedule', [
    'middleware' => 'auth',
    'uses' => 'UserController@showDeliverySchedule'
]);
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

Route::get('/stripe/webhook', 'SubinvoiceController@recordStripeInvoice'); //TODO :: comment after, this is for testing only
Route::post('/stripe/webhook', 'SubinvoiceController@recordStripeInvoice');
Route::post('/stripe/invoice/created', 'SubinvoiceController@checkForCredits');
Route::get('/stripe/test/webhook', 'SubinvoiceController@testStripeInvoice');
Route::get('/admin/services/invoice/testjson', 'SubinvoiceController@testStripeJSON');
Route::get('/admin/services/invoice/testshipxml', 'SubinvoiceController@testShippingStatus');

//ship station routes
Route::get('/shipstation/teststatus','SubinvoiceController@testShippingStatus');
Route::get('/shipstation/getorders','SubinvoiceController@getOrderXML');
Route::get('/shipstation/test','SubinvoiceController@getOrderXMLTest');
Route::post('/shipstation/getorders','SubinvoiceController@updateShippingStatus');
Route::get('/shipstation/test/invoice/{invoiceid}', 'SubinvoiceController@TestOrderXMLUser');

//cancellation, reactivation, hold routes
Route::get('/cancel/{id}', 'SubinvoiceController@CancelSubscription');
Route::get('/cancel/restart/{id}', 'SubinvoiceController@RestartSubscription');
Route::get('/hold/{id}/{holddate}', 'SubinvoiceController@HoldSubscription');
Route::get('/hold/restart/{id}/{holddate}', 'SubinvoiceController@UnHoldSubscription');
Route::get('/hold/restart/stripe/{id}/{holddate}', 'SubinvoiceController@ProcessUnHoldSubscription');
Route::get('/hold/check/{id}/{holddate}', 'SubinvoiceController@CheckForHold');
Route::get("/test/date/", 'SubinvoiceController@TestDate');
Route::get('/hold/checkall/', 'SubinvoiceController@CheckHolds');
Route::get('/test/menucontent/', 'SubinvoiceController@testMenus');
Route::get('/restart/held/{holddate}', 'SubinvoiceController@ReleaseAllHoldsByDate');

//coupon checker
Route::get('/coupon/getamount/{price}/{couponcode}', 'NewUserController@CheckCoupon');

//change plan
Route::get('/plan/childchange/{id}/{numchildren}/{weekof}', 'NewUserController@ChangeRatePlan');

//chron job
Route::get ('/run/scheduled/tasks/', 'CronTasksController@RunTasks');

$router->group(['middleware' => 'admin'], function($router) {
    Route::get('/admin/whatscooking/{id?}', 'WhatsCookingsController@showWhatsCookings');

    Route::get('/admin/users', 'UserController@showUsers');
    Route::get('/admin/users/updateListParams/{type}/{value?}', 'UserController@updateListParams');

    Route::get('/admin/user/{id}', 'UserController@showUser');
    Route::get('/admin/user_details/{id}', 'DashboardController@showUserDetails');
    Route::get('/admin/user_details/{id}/edit_shipping_address/{shId}', 'DashboardController@EditShippingAddress');
    Route::post('/admin/user_details/{id}/edit_shipping_address/{shId}', 'DashboardController@SaveShippingAddress');
    Route::get('/admin/user_details/{id}/edit_product', 'DashboardController@EditUserProduct');
    Route::post('/admin/user_details/{id}/edit_product', 'DashboardController@SaveUserProduct');

    Route::get('/admin/user_details/{id}/edit_menus/{dDate}', 'DashboardController@EditMenus');
    Route::post('/admin/user_details/{id}/edit_menus/{dDate}', 'DashboardController@SaveMenus');

    Route::get('/admin/user_details/{id}/skip_delivery/{dDate}', 'DashboardController@SkipDelivery');
    Route::get('/admin/user_details/{id}/unskip_delivery/{dDate}', 'DashboardController@UnskipDelivery');


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
	Route::post('/admin/user_details/credit/{id}', 'SubinvoiceController@issueCredit');

    Route::post('/admin/user/send_cancel_link/{id}', 'UserController@sendCancelLink');
    Route::post('/admin/user/cancel/restart/{id}', 'DashboardController@RestartSubscription');
    Route::post('/admin/user/cancel_now/{id}', 'UserController@CancelNow');


    Route::get('/admin/dashboard', 'DashboardController@show');
    Route::get('/admin/reports', 'DashboardController@showReports');
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

    Route::get('/admin/test', 'UserController@adminTest');
});


Route::get('user/test/{id}', 'UserController@showTest');

Route::get('/__test__/test/{email}', function($email) { //TODO :: remove or comment after all debugging is done
    $user = \App\User::where('email', $email)->first();
    echo \App\Cancellation::GenerateCancelLink($user);

//    \Illuminate\Support\Facades\Artisan::call('test:command');
//    echo "ok";
});


