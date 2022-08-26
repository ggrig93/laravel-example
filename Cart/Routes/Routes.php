<?php

namespace Modules\Cart\Routes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Cart\Controllers\CartController;
use Modules\Cart\Controllers\CartProductController;

Route::prefix('api/v1/carts')->middleware(['api','defineCity'])->group(function () {

    Route::get('{cartId}', [CartController::class, 'show'])->name('cart_show');
    Route::post('{cartId}/items', [CartProductController::class, 'create'])->name('cart_product_create');
    Route::patch('{cartId}/items/{itemId}', [CartProductController::class, 'updateItem'])->name('cart_edit_item');
    Route::delete('{cartId}/items', [CartProductController::class, 'delete'])->name('cart_delete');
    Route::delete('{cartId}/items/{itemId}', [CartProductController::class, 'deleteItem'])->name('cart_delete_item');

    $middleware = [];
    $authorizationHeader = \request()->header('authorization');

    if ($authorizationHeader) {
        $middleware[] = 'auth:sanctum';
    }
    Route::group(['middleware' => $middleware], function () {
        Route::patch('{cartId}', [CartController::class, 'update'])->name('cart_edit');
    });
});



Route::group(['prefix' => 'api/v1'], function () {
    $authorizationHeader = \request()->header('authorization');
    $middleware = ['api', 'defineCity'];

    if ($authorizationHeader) {
        $middleware[] = 'auth:sanctum';
    }
    Route::middleware($middleware)->post('cart',  [CartController::class, 'create'])->name('cart_get');
});


Route::prefix('api/v1')->middleware(['api', 'auth:sanctum', 'defineCity'])->get('cart/{userId}',  [CartController::class, 'getAuthCart'])->name('auth_cart_get');
Route::prefix('api/v1/cart')->middleware(['api', 'defineCity'])->get('{cart_id}/refresh_delay',  [CartController::class, 'getNearDateTime'])->name('near_date_time');
