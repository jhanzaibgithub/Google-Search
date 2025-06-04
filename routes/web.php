<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleSearchController;

Route::get('/', [GoogleSearchController::class, 'index'])->name('google.search');
