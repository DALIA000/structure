<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Setting\SettingInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(private SettingInterface $SettingI, public ResponseService $responseService)
    {
        $this->SettingI = $SettingI;
    }

    public function findBySlug(Request $request, $slug)
    {
        $setting = $this->SettingI->findBySlug($slug);

        if (!$setting) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        $data = new SettingResource($setting);
        return $this->responseService->json('Success!', $data, 200);
    }
}


