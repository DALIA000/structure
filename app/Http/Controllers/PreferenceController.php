<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Preference\PreferenceInterface;
use App\Http\Resources\PreferenceResource;
use App\Services\ResponseService;

class PreferenceController extends Controller
{
    public function __construct(public PreferenceInterface $PreferenceI, public ResponseService $responseService)
    {
    }

    public function preferences(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
        ]);

        $models = $this->PreferenceI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = PreferenceResource::collection($models['data']);

        return $this->responseService->json('Success!', ['preferences' => $data], 200, paginate: 'preferences');
    }
}
