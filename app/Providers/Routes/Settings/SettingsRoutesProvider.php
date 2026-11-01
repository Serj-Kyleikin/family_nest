<?php

namespace App\Providers\Routes\Settings;

use App\Http\Controllers\Api\Settings\{
    SettingsDiaryController,
    SettingsExpensesController,
};
use Illuminate\Support\Facades\Route;

class SettingsRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'settings'], function () {

            Route::group(['prefix' => 'expenses'], function () {
                Route::get('', [SettingsExpensesController::class, 'show']);
                Route::put('', [SettingsExpensesController::class, 'update']);
            });

            Route::group(['prefix' => 'diary'], function () {
                Route::get('', [SettingsDiaryController::class, 'show']);
                Route::put('', [SettingsDiaryController::class, 'update']);
            });
        });
    }
}
