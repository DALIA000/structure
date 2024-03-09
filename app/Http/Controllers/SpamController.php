<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Spam\SpamInterface;
use App\Http\Resources\SpamsListResource;
use App\Services\ResponseService;

class SpamController extends Controller
{
    public function __construct(public SpamInterface $SpamI, public ResponseService $responseService)
    {
    }

    public function spams(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'locale',
        ]]);

        $models = $this->SpamI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = SpamsListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['spams' => $data], 200, paginate: 'spams');
    }
}
