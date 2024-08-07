<?php
namespace App\Http\Controllers\Api;
//use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CronJobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 

Route::get('temp_crop', [CronJobController::class,'temp_crop']);

Route::post('register', [ApiController::class,'register'])->middleware('log.route');;
Route::post('login', [ApiController::class,'login'])->middleware('log.route');;
Route::post('resendOTP', [ApiController::class,'resendOTP']);
Route::post('verifyOTP', [ApiController::class,'verifyOTP']);
Route::post('forgotPassword', [ApiController::class,'forgotPassword']);
Route::post('changePassword', [ApiController::class,'changePassword']);

 
Route::post('businessList', [ApiController::class,'businessList'])->middleware('log.route');
Route::post('businessDetails', [ApiController::class,'businessDetails']);
Route::post('categoryList', [ApiController::class,'categoryList']);
Route::post('itemDetail', [ApiController::class,'itemDetail']);
Route::post('countryList', [ApiController::class,'countryList']);
Route::post('cityList', [ApiController::class,'cityList']);

 

 Route::group(['middleware' => 'app-api'], function(){ 
    // Route::post('create_profile', [ApiController::class,'createProfile']);
  
    Route::post('updatePassword', [ApiController::class,'updatePassword']);
    Route::post('updateProfile', [ApiController::class,'updateProfile']);
    Route::post('updateImage', [ApiController::class,'updateImage']);
    Route::get('logout', [ApiController::class,'logout']);
    Route::get('deleteUser', [ApiController::class,'deleteUser']);
    Route::post('addReview', [ApiController::class,'addReview']);
 
    Route::post('Addfavourite', [ApiController::class,'Addfavourite'])->middleware('log.route');
    Route::post('favouriteList', [ApiController::class,'favouriteList']);
    Route::post('feedsList', [ApiController::class,'feedsList']);

    Route::post('addCart', [ApiController::class,'addCart'])->middleware('log.route');
    Route::post('deleteCart', [ApiController::class,'deleteCart']);
    Route::post('updateCart', [ApiController::class,'updateCart'])->middleware('log.route');    
    Route::post('cartList', [ApiController::class,'cartList']);
    Route::post('notificationSettingUpdate', [ApiController::class,'notificationSettingUpdate']);
    Route::post('notificationSetting', [ApiController::class,'notificationSetting']);

    Route::post('createOrder', [ApiController::class,'createOrder']);
    Route::post('orderList', [ApiController::class,'orderList']);
    Route::post('orderDetails', [ApiController::class,'orderDetails']);
    Route::post('cancelOrder', [ApiController::class,'cancelOrder']);

    Route::post('cardList', [ApiController::class,'cardList']);
    Route::post('deleteCard', [ApiController::class,'deleteCard']);
 

    
    Route::post('addShippingAddress', [ApiController::class,'addShippingAddress']);
    Route::post('updateShippingAddress', [ApiController::class,'updateShippingAddress']);
    Route::post('shippingAddressList', [ApiController::class,'shippingAddressList']);

    Route::post('notificationList', [ApiController::class,'notificationList']);

    
    Route::post('create_card_token', [ApiController::class,'createCardToken'])->middleware('log.route');
    Route::get('get_all_save_card', [ApiController::class,'getAllSavedCard'])->middleware('log.route');
    Route::post('generate_payment_token', [ApiController::class,'generatePaymentToken'])->middleware('log.route');
    Route::post('delete_card', [ApiController::class,'deleteCard'])->middleware('log.route');
    Route::post('update_card', [ApiController::class,'updateCard'])->middleware('log.route');

    // by pranav 

    Route::post('read_notification', [ApiController::class,'read_notification']);
    Route::post('raiseQuery', [ApiController::class,'raiseQuery'])->middleware('log.route');;

     

  });





