<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Video\VideoInterface;
use \App\Http\Requests\Dashboard\DeleteReporeable;
use App\Http\Resources\Dashboard\VideosListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public $VideoI;
    public $loggedinUser;

    public function __construct(VideoInterface $VideoI, public ResponseService $responseService){
        $this->VideoI = $VideoI;
        $this->loggedinUser = app('loggedinUser');
    }
    public function videos(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $models = $this->VideoI->models($request);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = VideosListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['videos' => $data], 200, paginate: 'videos');
    }

    public function delete(DeleteReporeable $request, $id)
    {
        $model = $this->VideoI->deleteVideo($request, $id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
