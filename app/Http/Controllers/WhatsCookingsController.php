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
    public function showWhatsCooking($id)
    {

		$whatscookings = WhatsCookings::orderBy('week_of','desc')->get();
		$menus = [];
		return view('whatscooking')->with(['whatscookings'=>$whatscookings,'menus'=>$menus]);

    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showWhatsCookings($id = null)
    {
		$whatscookings = WhatsCookings::orderBy('week_of','desc')->get();
		$last = isset($id) ? WhatsCookings::find($id) : '';
		return view('whatscooking')->with(['whatscookings'=>$whatscookings,'last'=>$last]);;
    }

	
    
    public function saveWhatsCooking(Request $request)
    {
	    $whatscookings = $request->all();
	    
    	$validator = Validator::make($whatscookings, [
	        'product_type' => 'required|max:255',
	        'menu_title' => 'required|max:255',
		    'menu_description' => 'required|max:1000',
	    ]);

	    if ($validator->fails()) {
	        return redirect('/admin/whatscooking')
	            ->withInput()
	            ->withErrors($validator);
	    }

     	$test = WhatsCookings::where('week_of', $request->week_of)
     			->where('product_type', $request->product_type)
     			->first();
     	
     	$id = isset($test) ? $test->id : WhatsCookings::Create($whatscookings)->id;

    	if (array_key_exists('image', $whatscookings)) {
    		$image = $request->file('image');
    	}   
    	
    	
    	$image = $request->file('image');
    	$datestamp = date("FY");

	    $menu = new Menus;
	    $menu->menu_title = $request->menu_title;
		$menu->menu_description = $request->menu_description;
		
		if ($image) {
	    	$filename = $datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
   		 	Storage::disk('s3')->put('/' . $filename, file_get_contents($image));
    		$imagename = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
			$menu->image = $imagename;
		}
	    $menu->save();
		$menu->whatscookings()->attach($id);
	    return redirect('/admin/whatscooking/'.$id);
    }
}
