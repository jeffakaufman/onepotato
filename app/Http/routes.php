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

Route::get('/menus', 'MenusController@showMenus');
Route::get('/menu/{id}', 'MenusController@showMenu');


//Route::get('/menus', 'MenusController@show');

Route::get('menu/edit/{id}', array('as' => 'menu.edit', function($id) 
    {
        return View::make('menu-edit') 
            ->with('Menu', Menu::find($id));
    }));

Route::post('menu/edit', function() {
	        //do something
});

Route::post('/menus', 'MenusController@saveMenu');

Route::get('user/new', 'UserController@newUser');
Route::post('user/new', 'UserController@createUser');
Route::get('/users', 'UserController@showUsers');
Route::get('/user/{id}', 'UserController@showUser');
Route::post('/user/{id}', 'UserController@updateUser');