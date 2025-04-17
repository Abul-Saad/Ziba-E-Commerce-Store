<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    function product(Request $request, $id)
        {
            $request = Product::find($id);
            return view('frontend.product',['product'=>$request]);
        }


}
