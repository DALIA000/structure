<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Promote\PromoteInterface;
use App\Http\Resources\Dashboard\PromotesListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class PromoteController extends Controller
{
    public $PromoteI;
    public $loggedinUser;

    public function __construct(PromoteInterface $PromoteI, public ResponseService $responseService){
        $this->PromoteI = $PromoteI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function promotes(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'user_type_class' => $this->loggedinUser?->user_type_class,
            'city' => $this->loggedinUser?->city->slug
        ]);

        $models = $this->PromoteI->models($request);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = PromotesListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['promotes' => $data], 200, paginate: 'promotes');
    }
}
