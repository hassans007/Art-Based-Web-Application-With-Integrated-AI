<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\GameScoreController;
use App\Http\Controllers\PublicArtworkController;
use App\Http\Controllers\TheArt\ArtDisplayController;
use App\Http\Controllers\TheArt\ArtInteractionController;
use App\Http\Controllers\TheArt\AiController;
use App\Http\Controllers\FilterDataController;
use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| Shared Entry Route
|--------------------------------------------------------------------------
*/
Route::get('/theArt', function () {
    return Inertia::render('Welcome', [
        'headerImage' => '/images/homeTop.jpg',
        'title' => 'Welcome',
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::prefix('theArt')->group(function () {
    Route::get('/home', [ArtDisplayController::class, 'index'])->name('home');
    Route::get('/artCatalog', [ArtDisplayController::class, 'catalog'])->name('artCatalog');
    Route::get('/about', [ArtDisplayController::class, 'about'])->name('about');
    Route::get('/artistCatalog', [ArtistController::class, 'artistCatalog'])->name('artistCatalog');
    Route::get('/{id}/artworkDetail', [ArtDisplayController::class, 'show'])->name('artworkDetails');
    Route::get('/{id}/artistDetail', [ArtistController::class, 'artistDetail'])->name('artistDetail');

    Route::get('/aimodel', [AiController::class, 'aimodel'])->name('aimodel');

    Route::post('/aimodel', [AiController::class, 'analyze']);
    Route::post('/generateFeedback', [AiController::class, 'generateFeedback']);

    Route::get('/testYourself', [ArtDisplayController::class, 'testYourself'])->name('testYourself');
});

Route::get('/search', [ArtInteractionController::class, 'search'])->name('search');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profileinfo', [ProfileController::class, 'myProfile'])->name('profile.info');

    // Route::get('/theArt', function () {
    //     return Inertia::render('Login', [
    //         'headerImage' => '/images/homeTop.jpg',
    //         'title' => 'Login',
    //         'canLogin' => Route::has('login'),
    //         'canRegister' => Route::has('register'),
    //     ]);
    // })->name('logout');

    // Community Uploads
    Route::prefix('theArt/community')->group(function () {
        Route::get('/', [PublicArtworkController::class, 'community'])->name('community');
        Route::get('/upload', [PublicArtworkController::class, 'upload'])->name('uploadArt');
        Route::post('/', [PublicArtworkController::class, 'save'])->name('artwork.store');
        Route::get('/{id}/communityArtworkDetail', [PublicArtworkController::class, 'show'])->name('public.artworkDetails');
        Route::get('/{id}/edit', [PublicArtworkController::class, 'edit'])->name('public.artwork.edit');
        Route::post('/{id}/update', [PublicArtworkController::class, 'save'])->name('public.artwork.update');
        Route::delete('/{id}/delete', [PublicArtworkController::class, 'destroy'])->name('user.public.destroy');

        // Public Artwork Interactions
        Route::post('/{id}/like', [PublicArtworkController::class, 'toggleLike'])->name('public.artwork.like');
        Route::post('/{id}/comment', [PublicArtworkController::class, 'addComment'])->name('public.artwork.comment');
        Route::delete('/{id}/comment', [PublicArtworkController::class, 'deleteComment'])->name('public.artwork.comment.delete');
    });

    // Main Artwork Interactions
    Route::post('/theArt/{id}/toggle-save', [ArtInteractionController::class, 'toggleSave'])->name('toggleSave');
    Route::post('/theArt/{id}/like', [ArtInteractionController::class, 'toggleLike'])->name('artwork.like');
    Route::post('/theArt/gameScore', [GameScoreController::class, 'store'])->name('gameScore')->middleware('auth');

    // Chat System
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatMessageController::class, 'index'])->name('chat.index');
        Route::post('/', [ChatMessageController::class, 'store'])->name('chat.store');
        Route::delete('/{id}', [ChatMessageController::class, 'destroy'])->name('chat.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', AdminMiddleware::class.':admin'])->group(function () {
        // Manage Main Artworks
        Route::get('/artworks/{id}/edit', [AdminController::class, 'edit'])->name('admin.artwork.edit');
        Route::get('/theArt/adminUpload', [AdminController::class, 'upload'])->name('adminUpload');
        Route::post('/theArt/adminStore', [AdminController::class, 'save'])->name('admin.artwork.store');
        Route::post('/theArt/{id}/adminUpdate', [AdminController::class, 'save'])->name('admin.artwork.update');
        Route::delete('/artworks/{id}', [AdminController::class, 'destroyMainArtwork'])->name('admin.artwork.destroy');

        // Dashboard & Approvals
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/managePublicArtworks', [PublicArtworkController::class, 'adminDashboard'])->name('admin.public.index');
        Route::patch('/public-artworks/{id}/approve', [PublicArtworkController::class, 'approve'])->name('admin.public.approve');

        // Clear Chat
        Route::delete('/chat', [ChatMessageController::class, 'clearAll'])->name('admin.chat.clear');
    });

    Route::get('/styles', [ArtInteractionController::class, 'getStyles'])->name('styles');
    Route::get('/nationalities', [ArtInteractionController::class, 'getNationalities'])->name('nationalities');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel Breeze or Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
