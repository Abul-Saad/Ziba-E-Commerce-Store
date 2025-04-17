<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //

    function category($id)
    {
        $category = Category::with('products')->find($id);
        return view('frontend.category',['category'=>$category]);
    }

}
