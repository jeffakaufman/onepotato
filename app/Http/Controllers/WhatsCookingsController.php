<?php

namespace App\Http\Controllers;

use App\MenuAssigner;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;
use stdClass;
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
    	$startDate = date('Y-m-d H:i:s', strtotime("+1 week"));
    	$endDate = date('Y-m-d H:i:s', strtotime("+13 weeks"));
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
     * @return Menus for a given week
     */
    public function showWhatsCookingsDate($week_of)
    {
		//Look, I know there is a better way but this was just easier. Make an object for each product type, put them in an array
		//json encode, hope it works for Beverly
		$menus  = false;
		
		$whatscookings = WhatsCookings::where('week_of',$week_of)->get();

		foreach ($whatscookings as $whatscooking) {
    		$menus = $whatscooking->menus()->orderBy('isOmnivore','desc')->orderBy('menu_title')->get();
		}

        if($menus) {
            foreach ($menus as $menu) {
                $menu->dietaryPreferenceNumber = $menu->getDietaryPreferencesNumber();
            }
            return $menus->toJson();
        } else {
            return \json_encode(false);
        }

    }


	/**
     * Update a menu.
     *
     * @return Response
     */
    public function updateWhatsCooking(Request $request)
    {
		$whatsCooking = $request->all();
			    
    	$validator = Validator::make($whatsCooking, [
	        'menu_title' => 'required|max:255',
	    ]);

	    if ($validator->fails()) {
	        return redirect('/admin/whatscooking')
	            ->withInput()
	            ->withErrors($validator);
	    }
	    

     	$test = WhatsCookings::where('week_of', $request->week_of)
     			->first();
     			
     	$id = isset($test) ? $test->id : WhatsCookings::Create($whatsCooking)->id;

    	if (array_key_exists('image', $whatsCooking)) {
    		$image = $request->file('image');
    	}   
    	//echo $id;
    	
    	$image = $request->file('image');
    	$pdf = $request->file('pdf');
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
        $menu->noDairy = $request->noDairy ? $request->noDairy : 0;
        $menu->noEgg = $request->noEgg ? $request->noEgg : 0;
        $menu->noSoy = $request->noSoy ? $request->noSoy : 0;
        $menu->oven = $request->oven ? $request->oven : 0;
        $menu->stovetop = $request->stovetop ? $request->stovetop : 0;
        $menu->slowcooker = $request->slowcooker ? $request->slowcooker : 0;
        $menu->vegetarianBackup = $request->vegetarianBackup;
        $menu->isVegetarian = $request->isVegetarian ? $request->isVegetarian : 0;
        $menu->isOmnivore = $request->isOmnivore ? $request->isOmnivore : 0;
        $menu->isNotAvailable = $request->isNotAvailable ? $request->isNotAvailable : 0;
		
		if ($image) {
	    	$filename = $datestamp.'/'.$request->menu_title. '_Image.' . $request->file('image')->guessExtension();
   		 	Storage::disk('s3')->put('/' . $filename, file_get_contents($image));
    		$imagename = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$filename;
			$menu->image = $imagename;
		}

        if ($pdf) {
            $filename = $datestamp.'/'.$request->menu_title. '_Pdf.' . $pdf->guessExtension();
            Storage::disk('s3')->put('/' . $filename, file_get_contents($pdf));
            $pdfName = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$filename;
            $menu->pdf = $pdfName;
        }


 	    $menu->save();

     	if ($request->whatscooking_id != $id) {
     		$menu->whatscookings()->attach($id);
     		$menu->whatscookings()->detach($request->whatscooking_id);
     	}

        $weeksMenuCount = WhatsCookings::where('week_of', $whatsCooking['week_of'])->first()->menus()->get()->count();
        if(4 < $weeksMenuCount) { //at least 5 menus exist for updated cooking date
            MenuAssigner::ReassignAllForDate(new \DateTime($whatsCooking['week_of']), false, "Reassigned after admin changed menu item");
        }

	    return redirect('/admin/whatscooking/'.$id); 
    }

	
    
    public function saveWhatsCooking(Request $request)
    {
	    $whatscookings = $request->all();
    
    	$validator = Validator::make($whatscookings, [
	        'menu_title' => 'required|max:255',
	    ]);

	    if ($validator->fails()) {
	        return redirect('/admin/whatscooking')
	            ->withInput()
	            ->withErrors($validator);
	    }

     	$test = WhatsCookings::where('week_of', $whatscookings['week_of'])
     			->first();
     	
     	$id = isset($test) ? $test->id : WhatsCookings::Create($whatscookings)->id;

    	if (array_key_exists('image', $whatscookings)) {
    		$image = $request->file('image');
    	}   
    	
    	
    	$image = $request->file('image');
    	$pdf = $request->file('pdf');
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
        $menu->noDairy = $request->noDairy ? $request->noDairy : 0;
        $menu->noEgg = $request->noEgg ? $request->noEgg : 0;
        $menu->noSoy = $request->noSoy ? $request->noSoy : 0;
        $menu->oven = $request->oven ? $request->oven : 0;
        $menu->stovetop = $request->stovetop ? $request->stovetop : 0;
        $menu->slowcooker = $request->slowcooker ? $request->slowcooker : 0;
        $menu->vegetarianBackup = $request->vegetarianBackup ? $request->vegetarianBackup : 0;
        $menu->isVegetarian = $request->isVegetarian ? $request->isVegetarian : 0;
        $menu->isOmnivore = $request->isOmnivore ? $request->isOmnivore : 0;
        $menu->isNotAvailable = $request->isNotAvailable ? $request->isNotAvailable : 0;
		
		if ($image) {
	    	$filename = $datestamp.'/'.$request->menu_title. '_Image.' . $request->file('image')->guessExtension();
   		 	Storage::disk('s3')->put('/' . $filename, file_get_contents($image));
    		$imagename = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$filename;
			$menu->image = $imagename;
		}   
	   
		if ($pdf) {
	    	$filename = $datestamp.'/'.$request->menu_title. '_Pdf.' . $pdf->guessExtension();
   		 	Storage::disk('s3')->put('/' . $filename, file_get_contents($pdf));
    		$pdfName = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$filename;
			$menu->pdf = $pdfName;
		}

		$menu->save();


		$menu->whatscookings()->attach($id);
		$weeksMenuCount = WhatsCookings::where('week_of', $whatscookings['week_of'])->first()->menus()->get()->count();

  		if ( 4 < $weeksMenuCount ) {//assign all unassigned meals if this week has 5 meals
			//assign vegetarian replacement
			MenuAssigner::ReassignAllForDate(new \DateTime($whatscookings['week_of']), false, "Reassigned after admin added menu item");
		}
		return redirect('/admin/whatscooking/'.$id);
    }
}
