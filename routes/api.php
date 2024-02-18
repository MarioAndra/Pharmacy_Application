<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Models\User;
use App\Models\Medicin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('category/{warehouse_id}',[UserController::class,'BrowseMedicinByCategory']);
Route::post('regester',[AuthController::class,'regester']);
Route::post('login',[AuthController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout',[AuthController::class,'logout']);


    Route::get('serchMedicin',[UserController::class,'serchMedicin']);
    Route::get('user/show/orders', [UserController::class, 'ShowOrders']);
    Route::post('order/{warehouse_id}', [UserController::class, 'order']);

    });
    Route::get('NameWareHouse',[UserController::class,'NameWareHouse']);
Route::post('Adminregester',[AdminController::class,'Adminregester']);
Route::post('loginAdmin',[AdminController::class,'loginAdmin']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logoutAdmin',[AdminController::class,'logoutAdmin']);
    Route::post('addMedicin',[AdminController::class,'addMedicin']);
    Route::post('update/order/{orderId}', [AdminController::class, 'UpdateOrderStatus']);
    Route::get('admin/show/orders', [AdminController::class, 'ShowOrdersAdmin']);
    });
   // Route::post('createWareHouse',[AdminController::class, 'createWareHouse']);





