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
				->select('delivery_date','menu_title','hasBeef','hasPoultry','hasFish','hasLamb','hasPork','hasShellfish', DB::raw('count(*) as total'))
	    		->join('menus','menus_users.menus_id','=','menus.id')
				->groupBy('delivery_date','menus_id')
				->orderBy('delivery_date')
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
				->select('start_date', DB::raw('count(*) as total'))
				->groupBy('start_date')
				->orderBy('start_date')
				->where('start_date', ">",date('Y-m-d H:i:s'))
				->get();
       
        //echo json_encode($subs);
        return view('admin.dashboard')->with(['menus'=>$menus,'oldDate'=>'','meat'=>$meat,'newSubs'=>$newSubs]);
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
