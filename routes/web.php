<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use App\Models\Request;
use Illuminate\Support\Facades\Route;

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


Route::get("/",[AuthController::class,"index"])->name("login");
Route::get("/login",[AuthController::class,"index"])->name("login");
Route::post("/authentication/login",[AuthController::class,"login"]);
Route::get("/authentication/logout",[AuthController::class,"logout"])->name("logout");




Route::get("/solicitudes/list",[RequestController::class,"list"]);
Route::get("/solicitudes/view/{solicitude}",[RequestController::class,"view"])->name("solicitudes.view");
Route::post("/solicitudes/{solicitude}",[RequestController::class,"update"]);

Route::get("/solicitudes/agents/{type_id}",[RequestController::class,"agents"]);


Route::resource("/solicitudes",RequestController::class);

