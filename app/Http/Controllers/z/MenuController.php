<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class MenuController extends Controller
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
	
	
		return view('menu');
		
    }
	

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showMenu($id)
    {
	
		$menu = Menus::find($id);
		return view('menu');
		
    }


}
