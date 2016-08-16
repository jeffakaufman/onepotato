<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Menus;
use App\MenusUsers;
use App\User;
use App\Shippingholds;


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
  
    	$menus = DB::table('menus_users')
				->select('delivery_date','menu_title','products.product_title','hasBeef','hasPoultry','hasFish','hasLamb','hasPork','hasShellfish',DB::raw('count(*) as total'))
	    		->join('menus','menus_users.menus_id','=','menus.id')
	    		->join('users','menus_users.users_id','=','users.id')
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->groupBy('delivery_date','menus_id','product_title')
				->orderBy('delivery_date')
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
				->get();
       
        //echo json_encode($subs);
        return view('admin.dashboard')->with(['menus'=>$menus,'oldDate'=>'','oldMenu'=>'','meat'=>$meat,'newSubs'=>$newSubs,'totalSubs'=>$totalSubs]);
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
