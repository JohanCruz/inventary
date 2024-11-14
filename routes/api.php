<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\productController;
use App\Http\Controllers\api\orderController;
 

Route::get('/products', [productController::class, 'index' ]);

Route::get('/products/{id}', [productController::class, 'show' ] );

Route::post('/products', [productController::class, 'store' ]);

Route::patch('/products/{id}', [productController::class, 'update' ]);

Route::delete('/products/{id}', [productController::class, 'delete' ] );

Route::get('/orders',[orderController::class, 'index' ]);

Route::get('/orders/{id}', [orderController::class, 'show' ] );

Route::post('/orders', [orderController::class, 'store' ]);

Route::put('/orders/{id}', [orderController::class, 'updateOrder' ]);

Route::delete('/orders/{id}', [orderController::class, 'cancelOrder' ] );