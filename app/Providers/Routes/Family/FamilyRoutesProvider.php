<?php

namespace App\Providers\Routes\Family;

use App\Http\Controllers\Api\Family\{
    FamilyGroupController,
    FamilyLinkController,
    FamilyLinkRequestController,
    FamilyMemberController,
};
use Illuminate\Support\Facades\Route;

class FamilyRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'family'], function () {

            Route::group(['prefix' => 'group'], function () {

                Route::post('', [FamilyGroupController::class, 'create']);
                Route::get('', [FamilyGroupController::class, 'show']);
                Route::put('', [FamilyGroupController::class, 'update']);
                Route::delete('', [FamilyGroupController::class, 'destroy']);

                Route::group(['prefix' => 'link'], function () {

                    Route::post('', [FamilyLinkController::class, 'create']);
                    Route::get('', [FamilyLinkController::class, 'show']);
                });

                Route::post('join', [FamilyLinkRequestController::class, 'store']);

                Route::group(['prefix' => 'requests'], function () {
                    Route::get('', [FamilyLinkRequestController::class, 'index']);
                    Route::put('{user_id}/add', [FamilyLinkRequestController::class, 'addByUserId'])
                        ->where('user_id', '[0-9]+');
                });

                Route::group(['prefix' => 'members'], function () {

                    Route::get('', [FamilyMemberController::class, 'index']);

                    Route::group(['prefix' => '{user_id}', 'where' => ['user_id' => '[0-9]+']], function () {

                        Route::get('', [FamilyMemberController::class, 'show']);
                        Route::delete('', [FamilyMemberController::class, 'destroy']);
                    });
                });
            });
        });
    }
}
