<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Product;


class ProductsController extends Controller
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
    public function subscriptionList()
    {
        $products = Product::get();
        return view('admin.products.subscription_products')->with(['products'=>$products]);;
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function oneTimeList()
    {
        return view('one_time_products');
    }

}
