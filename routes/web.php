<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Container\Attributes\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\StripeController;
use App\RolesEnum;

Route::get('/', [ProductController::class, 'home'])->name('dashboard')
->middleware('auth:sanctum');

Route::get('/product/{product:slug}', [ProductController::class, 'show'])
->name('product.show');

Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

//Route::get('/dashboard', function () {
 //   return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['verified'])->group(function () {
        Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

        Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
        Route::get('/stripe/failure', [StripeController::class, 'failure'])->name('stripe.failure');

        Route::post("/become-a-vendor", [VendorController::class, 'store'])->name('vendor.store');
        Route::post("/update-vendor", [VendorController::class, 'store'])->name('vendor.update');
        Route::post('/stripe/connect', [StripeController::class, 'connect'])->name('stripe.connect')->middleware('role:'
     . RolesEnum::Vendor->value);

    });
});

Route::controller(CartController::class)->group(function() {
    Route::get('/cart', 'index')->name('cart.index')->middleware('auth:sanctum');
    Route::post('/cart/add/{product}',  'store')
    ->name('cart.store');
    Route::put('/cart/{product}', 'update')->name('cart.update');
    Route::delete('/cart/{product}', 'destroy')->name('cart.destroy');

});



require __DIR__.'/auth.php';
