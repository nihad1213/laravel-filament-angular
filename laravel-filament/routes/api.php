<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Http\Controllers\Api\ApiGameController;
use App\Http\Controllers\Api\ApiPlatformController;

Route::prefix('v1')->group(function () {
    Route::get('platforms', [ApiPlatformController::class, 'index']);

    Route::get('games', [ApiGameController::class, 'index']);
    Route::get('games/{game}', [ApiGameController::class, 'show']);

    Route::get('categories', [ApiCategoryController::class, 'index']);
});
