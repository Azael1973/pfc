<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ListingImageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Home -> listado de anuncios
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas públicas de listados
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->whereNumber('listing')->name('listings.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Subida y gestión de imágenes de anuncios
    Route::post('/listings/{listing}/images', [ListingImageController::class, 'store'])->name('listings.images.store');
    Route::delete('/listings/{listing}/images/{image}', [ListingImageController::class, 'destroy'])->name('listings.images.destroy');
    Route::patch('/listings/{listing}/images/{image}/order', [ListingImageController::class, 'reorder'])->name('listings.images.order');

    // Anuncios (crear/editar/borrar)
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::patch('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::get('/my-listings', [ListingController::class, 'mine'])->name('listings.mine');
    Route::get('/listings/{listing}/checkout', [ListingController::class, 'checkout'])->name('listings.checkout');
    Route::post('/listings/{listing}/checkout', [ListingController::class, 'selectPayment'])->name('listings.checkout.select');
    Route::get('/listings/{listing}/checkout/card', [ListingController::class, 'checkoutCardForm'])->name('listings.checkout.card');
    Route::post('/listings/{listing}/checkout/card', [ListingController::class, 'processCardPayment'])->name('listings.checkout.card.process');
    Route::get('/listings/{listing}/checkout/paypal', [ListingController::class, 'checkoutPaypalForm'])->name('listings.checkout.paypal');
    Route::post('/listings/{listing}/checkout/paypal', [ListingController::class, 'processPaypalPayment'])->name('listings.checkout.paypal.process');
    Route::get('/points', [PointsController::class, 'index'])->name('points.index');
    Route::post('/points/redeem', [PointsController::class, 'redeem'])->name('points.redeem');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/sold', [OrderController::class, 'sold'])->name('orders.sold');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Ofertas sobre un anuncio
    Route::get('/listings/{listing}/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::post('/listings/{listing}/offers', [OfferController::class, 'store'])->name('offers.store');
    Route::patch('/offers/{offer}/status', [OfferController::class, 'updateStatus'])->name('offers.updateStatus');
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');

    // Favoritos del usuario
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/listings/{listing}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/listings/{listing}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // Conversaciones y mensajes
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');

    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
});

require __DIR__.'/auth.php';
