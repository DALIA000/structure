<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ClubFeature\ClubFeatureInterface;
use App\Http\Resources\ClubFeatureResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ClubFeatureController extends Controller
{
    public function __construct(private ClubFeatureInterface $ClubFeatureI, private ResponseService $responseService)
    {
    }

    public function clubFeatures(Request $request, $username)
    {
        $request->merge([
            'username' => $username
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
}
