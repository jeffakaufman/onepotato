<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class RecipeController extends Controller
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
	
	
		return view('recipe');
		
    }
	

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showRecipe($id)
    {
	
		$menu = Recipes::find($id);
		return view('recipe');
		
    }


}
