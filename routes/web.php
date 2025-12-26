<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectLinkController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('dropdown', DropdownController::class)->name('dropdown');

    Route::prefix('dashboard/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'data'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard/clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/data', [ClientController::class, 'data'])->name('data');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard/contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/data', [ContactController::class, 'data'])->name('data');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dashboard/projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/data', [ProjectController::class, 'data'])->name('data');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');

        // Document routes
        Route::post('/{project}/documents', [ProjectDocumentController::class, 'store'])->name('documents.store');
        Route::delete('/{project}/documents/{media}', [ProjectDocumentController::class, 'destroy'])->name('documents.destroy');

        // Link routes
        Route::get('/{project}/links', [ProjectLinkController::class, 'index'])->name('links.index');
        Route::post('/{project}/links', [ProjectLinkController::class, 'store'])->name('links.store');
        Route::put('/{project}/links/{link}', [ProjectLinkController::class, 'update'])->name('links.update');
        Route::delete('/{project}/links/{link}', [ProjectLinkController::class, 'destroy'])->name('links.destroy');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
