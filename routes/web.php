<?php

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', static function (): View {
    return view('welcome');
});
