<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PromoteVideos\PromoteVideosInterface;
use App\Http\Resources\Dashboard\PromotesListResource;
use App\Http\Resources\PromotedVideosListResourse;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class PromoteVideosController extends Controller
{
    public $PromoteI;
    public $loggedinUser;

    public function __construct(PromoteVideosInterface $PromoteI, public ResponseService $responseService){
        $this->PromoteI = $PromoteI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function promote_videos(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $models = $this->PromoteI->models($request);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        $data = PromotedVideosListResourse::collection($models);

        return $this->responseService->json('Success!', ['promotes' => $data], 200, paginate: 'promotes');
    }
}
