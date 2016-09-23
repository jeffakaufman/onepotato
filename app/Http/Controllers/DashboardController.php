<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Menus;
use App\MenusUsers;
use App\User;
use App\Shippingholds;
use App\Subinvoice;


class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
//     * @return void
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
        // last week is the last week we have "shipped" invoices for
        $lastPeriodEndDate = Subinvoice::where('invoice_status','shipped')->max('period_end_date');
        $lastPeriodEndDate = date('Y-m-d',strtotime($lastPeriodEndDate));           
        $lastTuesday = date('Y-m-d',strtotime($lastPeriodEndDate.'-1 day'));         
        $thisTuesday = date('Y-m-d',strtotime($lastTuesday . '+7 days'));
        $nextTuesday = date('Y-m-d',strtotime($thisTuesday . '+7 days'));
        
        $shippingHoldsWeek = Shippingholds::whereIn('hold_status',['hold','held'])
				->where('date_to_hold', "=",$thisTuesday)
				->get(); 
		$skipIdsThisWeek = array_pluck($shippingHoldsWeek, 'user_id');
        
        //This week
		//yeah, i could've done this with a DB query but this seemed easier to read.
        $skipsThisWeek = User::whereIn('users.status',['active','inactive'])
				->where('start_date', "<=",$thisTuesday)
				->whereIn('id', $skipIdsThisWeek)
				->get();
				
        $activeThisWeek = User::whereIn('users.status',['active','inactive'])
				->where('start_date', "<=",$thisTuesday)
				->whereNotIn('id', $skipIdsThisWeek)
				->get();  

		//last week
		$shippingHoldsWeek = Shippingholds::whereIn('hold_status',['hold','held'])
				->where('date_to_hold', "=",$lastTuesday)
				->get(); 
		$skipIdsLastWeek = array_pluck($shippingHoldsWeek, 'user_id');
		
		$skipsLastWeek = User::whereIn('users.status',['active','inactive'])
				->where('start_date', "<=",$lastTuesday)
				->whereIn('id', $skipIdsLastWeek)
				->get();

        $shippedLastWeek = Subinvoice::where('invoice_status','shipped')
				->where('period_end_date', ">=",$lastPeriodEndDate)
				->get();    
		$skipIdsLastWeek = array_pluck($shippedLastWeek, 'user_id');
		
		$shippedLastWeek = User::whereIn('id', $skipIdsLastWeek)
				->get();
       
       //next week 
		$shippingHoldsWeek = Shippingholds::whereIn('hold_status',['hold','held'])
				->where('date_to_hold', "=",$nextTuesday)
				->get(); 
		$skipIdsNextWeek = array_pluck($shippingHoldsWeek, 'user_id');

        $skipsNextWeek = User::whereIn('users.status',['active','inactive'])
				->where('start_date', "<=",$nextTuesday)
				->whereIn('id', $skipIdsNextWeek)
				->get();

        $activeNextWeek = User::whereIn('users.status',['active','inactive'])
				->where('start_date', "<=",$nextTuesday)
				->whereNotIn('id', $skipIdsNextWeek)
				->get();    
        
        $nextTotalSubs = DB::table('users')
				->select('users.status', DB::raw('count(*) as total'))
				->leftJoin('shippingholds as shippingholds', 'shippingholds.user_id', '=', 'users.id')
				->groupBy('users.status')
				->orderBy('users.status')
				->get();
        
        $skips = DB::table('users')
				->select('date_to_hold', DB::raw('count(*) as total'))
	    		->join('shippingholds','shippingholds.user_id','=','users.id')
				->where('date_to_hold', ">=",date('Y-m-d H:i:s'))
				->where('hold_status', "=",'hold')
				->groupBy('date_to_hold')
				->orderBy('date_to_hold')
				->get();		

  
  		$menus = DB::table('menus_users')
				->select('delivery_date','menu_title','products.product_title','hasBeef','hasPoultry','hasFish','hasLamb','hasPork','hasShellfish',DB::raw('count(*) as total'))
	    		->join('menus','menus_users.menus_id','=','menus.id')
	    		->join('users','menus_users.users_id','=','users.id')
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->where('delivery_date', "=",$thisTuesday)
				->where('users.status', "<>","incomplete")
				->where('menus_users.menus_id', 62)
				->whereNotIn('users.id', $skipIdsThisWeek)
				->groupBy('delivery_date','menus_id','product_title')
				->orderBy('delivery_date')
				->orderBy('menus_id')
				->orderBy('products.id')
				->get();     
        $newSubs = DB::table('users')
				->select('start_date','products.product_description', DB::raw('count(*) as total'))
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->groupBy('start_date','products.product_description')
				->orderBy('start_date','products.product_description')
				->where('start_date', ">",date('Y-m-d H:i:s'))
				->where('subscriptions.stripe_id', "<>","0")
				->get();
        
        $totalNewSubs = DB::table('users')
				->select('subscriptions.status', DB::raw('count(*) as total'))
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->whereNotNull('subscriptions.status')
				->where('start_date', ">",date('Y-m-d H:i:s'))
				->where('subscriptions.stripe_id', "<>","0")
				->groupBy('subscriptions.status')
				->orderBy('subscriptions.status')
				->get();
/*
    	return view('admin.dashboard')
    			->with(['menus'=>$menus
    				,'oldDate'=>''
    				,'oldMenu'=>''
    				,'newSubs'=>$newSubs
    				,'activeThisWeek'=>$activeThisWeek
    				,'skipsThisWeek'=>$skipsThisWeek
    				,'activeNextWeek'=>$activeNextWeek
    				,'skipsNextWeek'=>$skipsNextWeek
    				,'skipsLastWeek'=>$skipsLastWeek
    				,'shippedLastWeek'=>$shippedLastWeek
    				,'thisTuesday'=>date('F d', strtotime($thisTuesday)) 
    				,'skips'=>$skips]
    			);
*/	
    }
    public function showReports()
    {
  
    	$menus = DB::table('menus_users')
				->select('delivery_date','menu_title','products.product_title','hasBeef','hasPoultry','hasFish','hasLamb','hasPork','hasShellfish',DB::raw('count(*) as total'))
	    		->join('menus','menus_users.menus_id','=','menus.id')
	    		->join('users','menus_users.users_id','=','users.id')
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->groupBy('delivery_date','menus_id','product_title')
				->orderBy('delivery_date', 'desc')
				->orderBy('menus_id')
				->orderBy('products.id')
				->get();
        
        $meat = DB::table('menus_users')
				->select(DB::raw('sum(hasBeef) as beef')
						,DB::raw('sum(hasPoultry) as poultry')
						,DB::raw('sum(hasFish) as fish')
						,DB::raw('sum(hasLamb) as lamb')
						,DB::raw('sum(hasPork) as pork')
						,DB::raw('sum(hasShellfish) as shellfish')
						)
	    		->join('menus','menus_users.menus_id','=','menus.id')
				->first();
        
        $newSubs = DB::table('users')
				->select('start_date','products.product_description', DB::raw('count(*) as total'))
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->groupBy('start_date','products.product_description')
				->orderBy('start_date','products.product_description')
				->where('start_date', ">",date('Y-m-d H:i:s'))
				->get();
        
        $totalSubs = DB::table('users')
				->select('products.product_description', DB::raw('count(*) as total'))
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->groupBy('products.product_description')
				->orderBy('products.product_description')
				->orderBy('products.id')
				->get();
       
        //echo json_encode($subs);
        return view('admin.reports')->with(['menus'=>$menus,'oldDate'=>'','oldMenu'=>'','meat'=>$meat,'newSubs'=>$newSubs,'totalSubs'=>$totalSubs]);
    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showRecipes()
    {

		$recipes = Recipes::get();
		return view('recipes')->with(['recipes'=>$recipes]);;

    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showRecipe($id)
    {
		$recipe = Recipes::find($id);
		return view('recipe')->with(['recipe'=>$recipe]);;

    }

	public function saveRecipe(Request $request) {
		/*
		
		$validator = Validator::make($request->all(), [
	        'recipe_title' => 'required|max:255',
		    'recipe_description' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/recipes')
	            ->withInput()
	            ->withErrors($validator);
	    }
*/
	    $recipe = new recipes;
	    $recipe->recipe_title = $request->recipe_title;
		$recipe->recipe_type = $request->recipe_type;
		$recipe->photo_url = $request->photo_url;
		$recipe->pdf_url = $request->pdf_url;
		$recipe->video_url = $request->video_url;
	    $recipe->save();

	    return redirect('/recipes');
	
	}
}
