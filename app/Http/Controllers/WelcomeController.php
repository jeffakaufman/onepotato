<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use stdClass;
use App\WhatsCookings;

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
        // if (Auth::check()) {
        // 	//$id = Auth::user()->id;
        // 	return redirect('/account'); 
        // } else {
        // 	return view('register-1')->with(['title'=> false, 'subtitle'=>false]);
        // }

        $tz = isset($_REQUEST['tz']) ? $_REQUEST['tz'] : 'America/Los_Angeles';
        date_default_timezone_set($tz);
        
        $weeksMenus = [];

        $nextTuesday = strtotime('next tuesday');
        $date = date('Y-m-d', $nextTuesday);

        $currentMenu = new stdClass;
        $whatscooking = WhatsCookings::where('week_of',$date)->first();

        $currentMenu->menus = [];
        if (isset($whatscooking)) {
            $currentMenu->menus = $whatscooking->menus()->get();
        } 

        $weeksMenus[] = $currentMenu->menus;

        return view('welcome')->with(['currentMenu'=>$weeksMenus]);

    }
}
