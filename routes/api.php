<?php

use App\Http\Controllers\V1\ArticleController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\SourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function () {
    Route::get('sources', [SourceController::class, 'index']);
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
});
