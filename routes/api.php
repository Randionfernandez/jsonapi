<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('articles/{article}', [ArticleController::class, 'show'] )->name('api.v1.articles.show');
Route::get('articles', [ArticleController::class, 'index'] )->name('api.v1.articles.index');
Route::post('articles', [ArticleController::class, 'store'] )->name('api.v1.articles.store');

