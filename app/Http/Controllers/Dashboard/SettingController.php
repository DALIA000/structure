<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Repositories\Setting\SettingInterface;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\Dashboard\SettingResource;

class SettingController extends Controller
{
    public function __construct(private SettingInterface $SettingI, public ResponseService $responseService)
    {
    }

    public function findBySlug(Request $request, $slug)
    {
        $request->merge([
            'locales',
        ]);
        
        $setting = $this->SettingI->findByWith('slug', $slug, $request);
        
        if (!$setting) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        $data = new SettingResource($setting);
        return $this->responseService->json('Success!', $data, 200);

    }

    public function update(Request $request, $slug)
    {
        $setting = $this->SettingI->update($request, $slug);
        
        if (!$setting) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$setting['status']) {
            return $this->responseService->json('Fail!', [], 400, $setting['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

}
