<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ClubFeature\ClubFeatureInterface;
use App\Http\Resources\Dashboard\ClubFeatureResource;
use App\Http\Resources\SettingResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ClubFeatureController extends Controller
{
    public function __construct(private ClubFeatureInterface $ClubFeatureI, private ResponseService $responseService)
    {
    }

    public function clubFeatures(Request $request, $id) // club_id
    {
        $request->merge([
            'club_id' => $id
        ]);
        $models = $this->ClubFeatureI->models($request);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound')]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = ClubFeatureResource::collection($models['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function update(Request $request, $id) // club id
    {
        $model = $this->ClubFeatureI->update($request, $id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}

