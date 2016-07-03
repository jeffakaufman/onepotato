<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;


class MenusController extends Controller
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
    public function showMenus()
    {

		$menus = Menus::get();
		return view('menus')->with(['menus'=>$menus]);;

    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showMenu($id)
    {

		$menu = Menus::find($id);
		return view('menu')->with(['menu'=>$menu]);;

    }
    
    public function uploadFileToS3(Request $request)
    {
    	$validator = Validator::make($request->all(), [
	        'menu_title' => 'required|max:255',
		    'menu_description' => 'required|max:1000',
		    'image' => 'required'
	    ]); 

	    if ($validator->fails()) {
	        return redirect('/admin/whatscooking')
	            ->withInput()
	            ->withErrors($validator);
	    }

    
    	$image = $request->file('image');
    	$datestamp = date("FY");
    	$filename = $datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();
    	
    	Storage::disk('s3')->put('/' . $filename, file_get_contents($image));
    	$imagename = "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/".$datestamp.'/'.$request->menu_title. '.' . $request->file('image')->guessExtension();

	    $menu = new Menus;
	    $menu->menu_title = $request->menu_title;
		$menu->menu_description = $request->menu_description;
		$menu->image = $imagename;
	    $menu->save();
	    $menu->whatscookings()->attach($request->whatscooking_id);
	    return redirect('/admin/whatscooking');

    	
    }
    
    

	public function saveMenu(Request $request) {
		
		
		$validator = Validator::make($request->all(), [
	        'menu_title' => 'required|max:255',
		    'menu_description' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/menus')
	            ->withInput()
	            ->withErrors($validator);
	    }

	    $menu = new Menus;
	    $menu->menu_title = $request->menu_title;
		$menu->menu_description = $request->menu_description;
	    $menu->save();

	    return redirect('/menus');
	
	}
}
