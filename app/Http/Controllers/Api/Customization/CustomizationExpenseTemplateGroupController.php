<?php

namespace App\Http\Controllers\Api\Customization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customization\{
    CustomizationExpenseTemplateGroupUpdateRequest,
};
use App\Services\Customization\CustomizationExpenseTemplateGroupService;
use App\Swagger\Api\Customization\CustomizationExpenseTemplateGroupController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\Http\{
    JsonResponse,
    Response,
};

class CustomizationExpenseTemplateGroupController extends Controller
{
    public function __construct(
        private readonly CustomizationExpenseTemplateGroupService $customizationExpenseTemplateGroupService,
    ) {
    }

    #[Swagger\Update]
    public function update(CustomizationExpenseTemplateGroupUpdateRequest $request): JsonResponse
    {
        $expenseTemplateGroupId = intval($request->input('expense_template_group_id'));
        $priority               = intval($request->input('priority'));

        try {
            $customization = $this->customizationExpenseTemplateGroupService->save($expenseTemplateGroupId, $priority);

            return response()->json([
                'status'    => true,
                'data'      => $customization,
                'message'   => 'Порядок категории сохранён',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
