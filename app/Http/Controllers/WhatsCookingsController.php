<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;


class WhatsCookingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
        return view('whatscooking');
    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showWhatsCookings()
    {

		$whatscookings = WhatsCookings::orderBy('week_of','desc')->get();
		//$menus = WhatsCookings::find($whatscookings[2]->id)->menus()->get();
		$menus = [];
		return view('whatscooking')->with(['whatscookings'=>$whatscookings,'menus'=>$menus]);

    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showWhatsCooking($id)
    {

		$whatscookings = WhatsCookings::orderBy('week_of','desc')->get();
		$menus = WhatsCookings::find($whatscookings[2]->id)->menus()->get();
		return view('whatscooking')->with(['whatscookings'=>$whatscookings,'menus'=>$menus]);;


//		$whatscookings = WhatsCookings::find($id);
		//$menus = $whatscookings->menus()->get();
		//echo $whatscookings->menus();
//		return view('whatscooking')->with(['whatscookings'=>$whatscookings,'menus'=>$whatscookings->menus()->get()]);;
//		return view('whatscooking')->with(['whatscookings'=>$whatscookings]);;

    }
    
	public function saveWhatsCooking(Request $request) {
		
		/*
		$validator = Validator::make($request->all(), [
	        'menu_title' => 'required|max:255',
		    'menu_description' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/menus')
	            ->withInput()
	            ->withErrors($validator);
	    }
*/
	    $whatscookings = new WhatsCookings;
	    $whatscookings->product_type = $request->product_type;
		$whatscookings->week_of = new \DateTime($request->week_of);
		//$whatscookings->menus()->attach(1);
	    $whatscookings->save();
	    return redirect('/admin/whatscooking');
	
	}
}
