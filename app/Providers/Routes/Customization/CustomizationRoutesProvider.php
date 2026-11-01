<?php

namespace App\Providers\Routes\Customization;

use App\Http\Controllers\Api\Customization\{
    CustomizationExpenseTemplateGroupController,
};
use Illuminate\Support\Facades\Route;

class CustomizationRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'customization'], function () {

            Route::group(['prefix' => 'expense-template-groups'], function () {
                Route::put('', [CustomizationExpenseTemplateGroupController::class, 'update']);
            });
        });
    }
}
