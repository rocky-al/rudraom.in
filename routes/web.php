<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});





Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('web.index');
    Route::get('aboutus', 'aboutUs')->name('web.aboutUs');
    Route::get('privacyPolicy', 'privacyPolicy')->name('web.privacyPolicy');
    Route::get('termsConditions', 'termsConditions')->name('web.termsConditions');
    
 
});



