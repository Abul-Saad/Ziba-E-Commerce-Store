<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    function home()
    {
        $products = Product::all();
        return view('frontend.index', ['products' => $products]);
    }


    }
