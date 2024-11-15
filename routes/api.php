<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\productController;
use App\Http\Controllers\api\orderController;
use App\Helpers\LogHelper;
 

Route::get('/products', [productController::class, 'index' ])
->middleware('intercept:index');

Route::get('/products/{id}', [productController::class, 'show'])
->middleware('intercept:show');

Route::post('/products', [productController::class, 'store'])
->middleware('intercept:store');

Route::patch('/products/{id}', [productController::class, 'update'])
->middleware('intercept:update');

Route::delete('/products/{id}', [productController::class, 'delete'])
->middleware('intercept:delete');

Route::get('/orders',[orderController::class, 'index'])
->middleware('intercept:index');

Route::get('/orders/{id}', [orderController::class, 'show'])
->middleware('intercept:show');

Route::post('/orders', [orderController::class, 'store'])
->middleware('intercept:store');

Route::put('/orders/{id}', [orderController::class, 'updateOrder'])
->middleware('intercept:updateOrder');

Route::delete('/orders/{id}', [orderController::class, 'cancelOrder'])
->middleware('intercept:cancelOrder');