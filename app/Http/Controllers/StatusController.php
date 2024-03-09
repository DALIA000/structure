<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Resources\{
    StatusListResource,
};
use App\Services\ResponseService;

class StatusController extends Controller
{
    public function __construct(private StatusInterface $StatusI, private ResponseService $responseService)
    {
        $this->StatusI = $StatusI;
    }

    public function status(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'Created_at']);
        }

        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'locale',
        ]]);

        $status = $this->StatusI->models($request);

        if (!$status) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$status['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $status['errors']]);
        }

        $data = StatusListResource::collection($status['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
