<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/user/'], function () {
    //User Route
        Route::post('register', [UserController::class, 'Register']);
        Route::post('send-otp',[UserController::class,'sendOtp']);
        Route::post('login',[UserController::class,'Login']);
    Route::group(['middleware'     => ['user']], function () {
        Route::post('edit-profile',[UserController::class,'editProfile']);
        Route::get('menu',[UserController::class,'GetMenu']);
        Route::post('item-detail',[UserController::class,'itemDetail']);
        Route::post('add-cart',[UserController::class,'addCart']);
        Route::get('user-cart',[UserController::class,'userCart']);
        Route::post('delete_cart',[UserController::class,'delete_cart']);
        Route::post('add-address',[UserController::class,'addAddress']);
        Route::post('edit-address',[UserController::class,'editAddress']);
        Route::get('user-address',[UserController::class,'userAddress']);
        Route::post('order',[UserController::class,'order']);
        Route::get('past-order',[UserController::class,'pastOrder']);
        Route::get('ongoing-order',[UserController::class,'ongoingOrder']);
        Route::post('order-detail',[UserController::class,'orderDetail']);
        Route::post('contact-us',[UserController::class,'contactUs']);
        Route::post('logout',[UserController::class,'logOut']);

        // Route::post('')
    });

});
Route::group(['prefix' => '/admin/'], function () {
    //User Route
        Route::post('send-otp',[AdminController::class,'sendOtp']);
        Route::post('login',[AdminController::class,'Login']);
    Route::group(['middleware'     => ['admin']], function () {
        Route::post('Active-inactive',[AdminController::class,'loginStatus']);
        Route::get('previous-order',[AdminController::class,'previousOrder']);
        Route::get('cancelled-order',[AdminController::class,'cancelledOrder']);    
        Route::get('ongoing-order',[AdminController::class,'ongoingOrder']);
        Route::get('upcoming-order',[AdminController::class,'upcomingOrder']);
        Route::get('cancelation-reasons',[AdminController::class,'cancelationReason']);
        Route::post('cancel-order',[AdminController::class,'cancelOrder']);
        Route::post('accept-order',[AdminController::class,'acceptOrder']);
        Route::post('out-for-delivery',[AdminController::class,'outDelivery']);
        Route::get('contact-us',[AdminController::class,'contactUs']);


        // Route::post('')
    });

});