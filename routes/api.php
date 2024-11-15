<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\productController;
use App\Http\Controllers\api\orderController;

Route::get('/products', function () {
    $controller = new ProductController();
    return intercept($controller, 'index');
});

Route::get('/products/{id}', function () {
    $controller = new ProductController();
    return intercept($controller, 'show');
}); 

Route::post('/products', function () {
    $controller = new ProductController();
    return intercept($controller, 'store');
});

Route::patch('/products/{id}', function () {
    $controller = new ProductController();
    return intercept($controller, 'update');
}); 

Route::delete('/products/{id}', function () {
    $controller = new ProductController();
    return intercept($controller, 'delete');
}); 

Route::get('/orders', function () {
    $controller = new orderController();
    return intercept($controller, 'index');
}); 

Route::get('/orders/{id}', function () {
    $controller = new orderController();
    return intercept($controller, 'show');
}); 

Route::post('/orders', function () {
    $controller = new orderController();
    return intercept($controller, 'store');
}); 

Route::put('/orders/{id}', function () {
    $controller = new orderController();
    return intercept($controller, 'updateOrder');
}); 

Route::delete('/orders/{id}', function () {
    $controller = new orderController();
    return intercept($controller, 'cancelOrder');
});
