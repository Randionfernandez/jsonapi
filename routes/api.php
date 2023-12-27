<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('articles/{article}', [ArticleController::class, 'show'] )->name('api.v1.articles.show');
Route::get('articles', [ArticleController::class, 'index'] )->name('api.v1.articles.index');
