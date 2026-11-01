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
                    Route::get('with-templates', [ExpenseTemplateGroupController::class, 'indexWithTemplates']);
                    Route::post('', [ExpenseTemplateGroupController::class, 'store']);

                    Route::group(['prefix' => '{id}', 'where' => ['id' => '[0-9]+']], function () {

                        Route::get('', [ExpenseTemplateGroupController::class, 'show']);
                        Route::put('', [ExpenseTemplateGroupController::class, 'update']);
                        Route::delete('', [ExpenseTemplateGroupController::class, 'destroy']);

                        Route::get('templates', [ExpenseTemplateController::class, 'index']);
                    });
                });

                Route::post('', [ExpenseTemplateController::class, 'store']);

                Route::group(['prefix' => '{id}', 'where' => ['id' => '[0-9]+']], function () {

                    Route::get('', [ExpenseTemplateController::class, 'show']);
                    Route::put('', [ExpenseTemplateController::class, 'update']);
                    Route::delete('', [ExpenseTemplateController::class, 'destroy']);
                });
            });

            Route::get('', [ExpenseController::class, 'index']);
            Route::post('', [ExpenseController::class, 'store']);

            Route::group(['prefix' => '{id}', 'where' => ['id' => '[0-9]+']], function () {
                Route::put('', [ExpenseController::class, 'update']);
                Route::delete('', [ExpenseController::class, 'destroy']);
            });
        });
    }
}
