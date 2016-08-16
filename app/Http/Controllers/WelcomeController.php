<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class WelcomeController extends Controller
{
    /**
     * Show the application splash screen.
     *
     * @return Response
     */
    public function show()
    {
        //return view('welcome');
        if (Auth::check()) {
        	//$id = Auth::user()->id;
        	return redirect('/delivery-schedule'); 
        } else {
        	return view('register-1');
        }
    }
}
