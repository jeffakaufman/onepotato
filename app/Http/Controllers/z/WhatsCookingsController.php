<?php

namespace App\Http\Controllers;

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
		$menus  = [];
		
		$whatscookings = WhatsCookings::where('week_of',$week_of)->get();
		foreach ($whatscookings as $whatscooking) {
    		$menus = $whatscooking->menus()->get();
		}
    		foreach ($menus as $menu) {
    			$menu->dietaryPreferenceNumber = $menu->getDietaryPreferencesNumber();
    		}
		return $menus->toJson();
    }


	/**
     * Update a menu.
     *
     * @return Response
     */
    public function updateWhatsCooking(Request $request)
    {
		$whatscooking = $request->all();
			    
    	$validator = Validator::make($whatscooking, [
	        'menu_title' => 'required|max:255',
		    'menu_description' => 'required|max:1000',
	    ]);

	    if ($validator->fails()) {
	        return redirect('/admin/whatscooking')
	            ->withInput()
	            ->withErrors($validator);
	    }
	    

     	$test = WhatsCookings::where('week_of', $request->week_of)
     			->first();
     			
     	$id = isset($test) ? $test->id : WhatsCookings::Create($whatscooking)->id;			

    	if (array_key_exists('image', $whatscooking)) {
    		$image = $request->file('image');
    	}   
    	//echo $id;
    	
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
	        'menu_title' => 'required|max:255',
		    'menu_description' => 'required|max:1000',
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
	    	$filename = $datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
   		 	Storage::disk('s3')->put('/' . $filename, file_get_contents($image));
    		$imagename = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
			$menu->image = $imagename;
		}   
	   
		$mainIngredientNumber =  "%".$menu->getDietaryPreferencesNumber()."%";

		$menu->save();
	    
	    //add new menu to subscribers
	    $deliveryDate = "'".$request->week_of."' as delivery_date";
	    $menusID = "'".$menu->id."' as menus_id";
	    
	    //find proper subscribers
	    if ( $request->isOmnivore && !$request->isVegetarian ) {
	    	$subs = DB::table('products')
	    		->where('product_type',2)
	    		->join('subscriptions','products.id','=','subscriptions.product_id')
	    		->where('subscriptions.dietary_preferences','like',$mainIngredientNumber)
	    		->get(['user_id as users_id',DB::raw($deliveryDate),DB::raw($menusID)]);
			$subs = json_decode(json_encode($subs), true); //i have to do this. i don't know why
			
	    	DB::table('menus_users')->insert($subs);
	    }
	    elseif ( !$request->isOmnivore && $request->isVegetarian ){
	       	$subs = DB::table('products')
	    		->where('product_type',1)
	    		->join('subscriptions','products.id','=','subscriptions.product_id')
	    		->get(['user_id as users_id',DB::raw($deliveryDate),DB::raw($menusID)]);
			$subs = json_decode(json_encode($subs), true); //i have to do this. i don't know why
	    	DB::table('menus_users')->insert($subs);
	    
	    }
	    elseif($request->isOmnivore && $request->isVegetarian){
	    	$subs = DB::table('products')
	    		->join('subscriptions','products.id','=','subscriptions.product_id')
	    		->get(['user_id as users_id',DB::raw($deliveryDate),DB::raw($menusID)]);
			$subs = json_decode(json_encode($subs), true); //i have to do this. i don't know why
	    	DB::table('menus_users')->insert($subs);
	    }
	    
	  
		
		$menu->whatscookings()->attach($id);
		$weeksMenuCount = WhatsCookings::where('week_of', $whatscookings['week_of'])->first()->menus()->get()->count();

  		if ( $weeksMenuCount >= 5 ) {//assign all unassigned meals if this week has 5 meals
			//assign vegetarian replacement
			
			//get the vegetarian backup for the week
			$vegetarianBackup =  WhatsCookings::where('week_of', $whatscookings['week_of'])->first()->menus()
									->where('vegetarianBackup','1')
									->first();

			//find the omnivore subscribers that are are missing at least one meal
			$subs = DB::table('menus_users')
					->select('users_id', DB::raw('count(*) as total'))
					->where('delivery_date', $whatscookings['week_of'])
                	->having('total', '<', 3)
					->groupBy('users_id')
					->get();
			echo json_encode($subs);
			//remove the total element from the objects because all life is pain
			if ($subs) {
				foreach($subs as $sub) {
					$scrubbedSubs[] = array(
						"users_id" => $sub->users_id,
						"delivery_date" => $request->week_of,
						"menus_id" => $vegetarianBackup->id
						); 
				}
				$scrubbedSubs = json_decode(json_encode($scrubbedSubs), true); //again, don't ask
				
				//add the replacement meal to the subscriber
	    		DB::table('menus_users')->insert($scrubbedSubs);
	    		}
	    		
	    		$vegetarianBackupBackup =  WhatsCookings::where('week_of', $whatscookings['week_of'])->first()->menus()
					->where('isOmnivore','0')
					->where('isVegetarian','1')
					->where('vegetarianBackup','0')
									->first();
					
	    		//find the omnivore subscribers that are missing only one meal
				$scrubbedSubs = [];
				$subs = DB::table('menus_users')
					->select('users_id', DB::raw('count(*) as total'))
					->where('delivery_date', $whatscookings['week_of'])
                	->having('total', '=', 2)
					->groupBy('users_id')
					->get();
			
				//remove the total element from the objects because all life is pain
				if ($subs) {
					foreach($subs as $sub) {
						$scrubbedSubs[] = array(
							"users_id" => $sub->users_id,
							"delivery_date" => $request->week_of,
							"menus_id" => $vegetarianBackupBackup->id
							); 
				}
				$scrubbedSubs = json_decode(json_encode($scrubbedSubs), true); //again, don't ask
				
				//add the final replacement meal to the subscriber
	    		DB::table('menus_users')->insert($scrubbedSubs);
	    	}
		}
		return redirect('/admin/whatscooking/'.$id);
    }
}
