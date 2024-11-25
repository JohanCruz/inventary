<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
 
//Route::get('/products/{id}', [ProductController::class, 'show' ], $id );
Route::get('/products/{id}', function ($id) {
    $controller = new ProductController();
    return intercept($controller, 'show', $id );
});

Route::get('/products', function () {
    $controller = new ProductController();
    return intercept($controller, 'index');
});

// Route::post('/products', [ProductController::class, 'store' ]);
Route::post('/products/{id}', function () {
    $controller = new ProductController();
    return intercept($controller, 'store' );
});

//Route::patch('/products/{id}', [ProductController::class, 'update' ], $id);
Route::patch('/products/{id}', function ($id) {
    $controller = new ProductController();
    return intercept($controller, 'update', $id );
});

// Route::delete('/products/{id}', [ProductController::class, 'delete' ], $id );
Route::delete('/products/{id}', function ($id) {
    $controller = new ProductController();
    return intercept($controller, 'delete', $id );
});

//Route::get('/orders/{id}', [OrderController::class, 'show' ], $id );
Route::get('/orders/{id}', function ($id) {
    $controller = new OrderController();
    return intercept($controller, 'show', $id );
});

// Route::get('/orders',[OrderController::class, 'index' ]);
Route::get('/orders', function () {
    $controller = new OrderController();
    return intercept($controller, 'index' );
});

//Route::post('/orders', [OrderController::class, 'store' ]);
Route::post('/orders', function () {
    $controller = new OrderController();
    return intercept($controller, 'store' );
});

//Route::put('/orders/{id}', [OrderController::class, 'updateOrder' ], $id);
Route::put('/orders/{id}', function ($id) {
    $controller = new OrderController();
    return intercept($controller, 'updateOrder', $id );
});

// Route::delete('/orders/{id}', [OrderController::class, 'cancelOrder' ], $id );
Route::delete('/orders/{id}', function ($id) {
    $controller = new OrderController();
    return intercept($controller, 'cancelOrder', $id );
});