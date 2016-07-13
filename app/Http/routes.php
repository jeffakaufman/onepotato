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

	
Route::post('/whatscooking', 'WhatsCookingsController@saveWhatsCooking');

Route::get('/admin/whatscooking', 'WhatsCookingsController@showWhatsCookings');
Route::get('/admin/whatscooking/{id}', 'WhatsCookingsController@showWhatsCooking');

Route::post('/stripe/webhook', 'SubinvoiceController@recordStripeInvoice');
Route::get('/admin/services/invoice/test', 'SubinvoiceController@testStripeInvoice');
Route::get('/admin/services/invoice/testjson', 'SubinvoiceController@testStripeJSON');
Route::get('/admin/services/invoice/testshipxml', 'SubinvoiceController@updateShippingStatus');


Route::get('/shipstation/getorders','SubinvoiceController@getOrderXML');
Route::post('/shipstation/getorders','SubinvoiceController@updateShippingStatus');




