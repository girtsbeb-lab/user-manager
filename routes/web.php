<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/users');

// Users CRUD
Route::resource('users', UserController::class);
Route::post('users/import', [UserController::class, 'import'])->name('users.import');

// Contact management (nested under user)
Route::get('users/{user}/contact/edit', [ContactController::class, 'edit'])->name('users.contact.edit');
Route::put('users/{user}/contact', [ContactController::class, 'update'])->name('users.contact.update');
Route::delete('users/{user}/contact', [ContactController::class, 'destroy'])->name('users.contact.destroy');

// Address management (nested under user)
Route::get('users/{user}/address/edit', [AddressController::class, 'edit'])->name('users.address.edit');
Route::put('users/{user}/address', [AddressController::class, 'update'])->name('users.address.update');
Route::delete('users/{user}/address', [AddressController::class, 'destroy'])->name('users.address.destroy');
