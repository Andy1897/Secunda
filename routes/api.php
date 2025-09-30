<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('api_key')
    ->prefix('v1')
    ->group(function () {
        // Организации (специальные маршруты впереди)
        Route::get('organizations', [OrganizationController::class, 'index']);
        // Фильтры по зданиям и видам деятельности
        Route::get('buildings/{building}/organizations', [OrganizationController::class, 'byBuilding']);
        Route::get('activities/{activity}/organizations', [OrganizationController::class, 'byActivity']);
        // Геопоиск
        Route::get('organizations/near', [OrganizationController::class, 'near']);
        Route::get('organizations/in-rect', [OrganizationController::class, 'inRect']);
        // Поиск по названию
        Route::get('organizations/search', [OrganizationController::class, 'search']);
        // По ID (в конце, чтобы не перехватывать near/in-rect/search)
        Route::get('organizations/{id}', [OrganizationController::class, 'show']);

        // Вспомогательные
        Route::get('buildings', [BuildingController::class, 'index']);
        Route::get('activities', [ActivityController::class, 'index']);
    });
