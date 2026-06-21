<?php

namespace App\Providers\Routes\Expense;

use App\Http\Controllers\Api\Expenses\{
    ExpenseController,
    ExpenseTemplateController,
    ExpenseTemplateGroupController,
};
use Illuminate\Support\Facades\Route;

class ExpenseRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'expenses'], function () {

            Route::group(['prefix' => 'template'], function () {

                Route::group(['prefix' => 'groups'], function () {
                    Route::get('', [ExpenseTemplateGroupController::class, 'index']);
                    Route::post('', [ExpenseTemplateGroupController::class, 'store']);
                    Route::get('{id}', [ExpenseTemplateGroupController::class, 'show']);
                    Route::put('{id}', [ExpenseTemplateGroupController::class, 'update']);
                    Route::delete('{id}', [ExpenseTemplateGroupController::class, 'destroy']);
                });

                Route::post('', [ExpenseTemplateController::class, 'store']);
                Route::get('{id}', [ExpenseTemplateController::class, 'show']);
                Route::put('{id}', [ExpenseTemplateController::class, 'update']);
                Route::delete('{id}', [ExpenseTemplateController::class, 'destroy']);
            });

            Route::get('', [ExpenseController::class, 'index']);
            Route::post('', [ExpenseController::class, 'store']);
            Route::put('{id}', [ExpenseController::class, 'update']);
            Route::delete('{id}', [ExpenseController::class, 'destroy']);
        });
    }
}
