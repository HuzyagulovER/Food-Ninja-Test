<?php

use App\Http\Controllers\Redirect;
use Illuminate\Support\Facades\Route;

Route::get('/l/{code}', [Redirect::class, 'show']);
