<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;
use App\WhatsCookings;
use App\Menus;


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
        return view('admin.whatscooking.whatscooking');
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
		return view('admin.whatscooking.whatscooking')->with(['whatscookings'=>$whatscookings,'menus'=>$menus]);

    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showWhatsCookings($id = null)
    {
    	$startDate = date('Y-m-d H:i:s');
    	$endDate = date('Y-m-d H:i:s', strtotime("+12 weeks"));
    	$upcomingDates = [];

    	for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime('+1 day', $i)) {
			if (date('N', $i) == 2) {//Tuesday == 2 {
				$upcomingDates[date('Y-m-d', $i)] = date('m/d/y', $i);
			}   
    	}
		$whatscookings = WhatsCookings::orderBy('week_of','desc')->get();
		$last = isset($id) ? WhatsCookings::find($id) : '';
		return view('admin.whatscooking.whatscooking')->with(['whatscookings'=>$whatscookings,'last'=>$last,'upcomingDates'=>$upcomingDates]);;
    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showWhatsCookingsDate($week_of)
    {

		$whatscookings = WhatsCookings::where('week_of',$week_of)->orderBy('product_type')->get();
		
		foreach ($whatscookings as $whatscooking) {
    		echo $whatscooking->menus()->get();
		}
		
		//return view('admin.whatscooking.whatscooking')->with(['whatscookings'=>$whatscookings,'last'=>$last,'upcomingDates'=>$upcomingDates]);;
    }


	/**
     * Update a menu.
     *
     * @return Response
     */
    public function updateWhatsCooking(Request $request)
    {
		//echo(implode(",",$request->all()));
		
		$whatscooking = $request->all();
			    
    	$validator = Validator::make($whatscooking, [
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
     			
     	$id = isset($test) ? $test->id : WhatsCookings::Create($whatscooking)->id;			

    	if (array_key_exists('image', $whatscooking)) {
    		$image = $request->file('image');
    	}   
    	
    	
    	$image = $request->file('image');
    	$datestamp = date("FY");

	    $menu = Menus::find($request->menu_id);
	    $menu->menu_title = $request->menu_title;
		$menu->menu_description = $request->menu_description;
        $menu->hasBeef = $request->hasBeef ? $request->hasBeef : 0;
        $menu->hasPoultry = $request->hasPoultry ? $request->hasPoultry : 0;
        $menu->hasFish = $request->hasFish ? $request->hasFish : 0;
        $menu->hasLamb = $request->hasLamb ? $request->hasLamb : 0;
        $menu->hasPork = $request->hasPork ? $request->hasPork : 0;
        $menu->hasShellfish = $request->hasShellfish ? $request->hasShellfish : 0;
        $menu->hasNoGluten = $request->hasNoGluten ? $request->hasNoGluten : 0;
        $menu->hasNuts = $request->hasNuts ? $request->hasNuts : 0;
		
		if ($image) {
	    	$filename = $datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
   		 	Storage::disk('s3')->put('/' . $filename, file_get_contents($image));
    		$imagename = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
			$menu->image = $imagename;
		}
 	    $menu->save();

     	if ($request->whatscooking_id != $id) {
     		$menu->whatscookings()->attach($id);
     		$menu->whatscookings()->detach($request->whatscooking_id);
     	}
      	    	
	    return redirect('/admin/whatscooking/'.$id); 
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

     	$test = WhatsCookings::where('week_of', $whatscookings['week_of'])
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
        $menu->hasBeef = $request->hasBeef ? $request->hasBeef : 0;
        $menu->hasPoultry = $request->hasPoultry ? $request->hasPoultry : 0;
        $menu->hasFish = $request->hasFish ? $request->hasFish : 0;
        $menu->hasLamb = $request->hasLamb ? $request->hasLamb : 0;
        $menu->hasPork = $request->hasPork ? $request->hasPork : 0;
        $menu->hasShellfish = $request->hasShellfish ? $request->hasShellfish : 0;
        $menu->hasNoGluten = $request->hasNoGluten ? $request->hasNoGluten : 0;
        $menu->hasNuts = $request->hasNuts ? $request->hasNuts : 0;
		
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
