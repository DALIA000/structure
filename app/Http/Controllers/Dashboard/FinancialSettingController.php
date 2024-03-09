<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\FinancialSetting\FinancialSettingInterface;
use App\Http\Requests\Dashboard\{
    CreateFinancialSettingRequest,
    UpdateFinancialSettingRequest,
};
use App\Http\Resources\Dashboard\{
    FinancialSettingsListResource,
};
use App\Services\ResponseService;

class FinancialSettingController extends Controller
{
    public function __construct(private FinancialSettingInterface $FinancialSettingI, private ResponseService $responseService)
    {
        $this->FinancialSettingI = $FinancialSettingI;
    }

    public function financialSettings(Request $request)
    {
        $financial_settings = $this->FinancialSettingI->models($request);

        if (!$financial_settings) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$financial_settings['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $financial_settings['errors']]);
        }

        $data = FinancialSettingsListResource::collection($financial_settings['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function edit(UpdateFinancialSettingRequest $request, $slug)
    {
        $financial_setting = $this->FinancialSettingI->update($request, $slug);

        if (!$financial_setting) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$financial_setting['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $financial_setting['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
