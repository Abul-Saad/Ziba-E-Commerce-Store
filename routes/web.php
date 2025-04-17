<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::get('/file', function () {
    return view('welcome');
});

Route::get('/',[HomeController::class,'home']);
Route::get('/productsCate/{id}',[HomeController::class,'productsCate']);
Route::get('/category/{id}',[CategoryController::class,'category']);
Route::get('/product/{id}',[ProductsController::class,'product']);
Route::get('/catproducts/{id}',[ProductsController::class,'categoryProducts']);