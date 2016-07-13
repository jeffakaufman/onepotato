<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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
        return view('subscription_products');
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
