<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Video\VideoInterface;
use App\Http\Resources\LikeHistoryListResource;
use App\Http\Resources\LikesUsersListResource;
use Illuminate\Http\Request;
use App\Http\Repositories\{
    Like\LikeInterface,
    Save\SaveInterface,
    Block\BlockInterface,
    Follow\FollowInterface,
    Report\ReportInterface,
};
use App\Services\ResponseService;
use App\Models\{
    Model,
    User
};

use App\Http\Resources\LikesListResource;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    public $loggedinUser;

    public function __construct(
        public LikeInterface $LikeI, 
        public ResponseService $responseService, 
        public SaveInterface $SaveI, 
        public BlockInterface $BlockI,
        public FollowInterface $FollowI,
        public ReportInterface $ReportI,
        public User $model,
        public VideoInterface $VideoI
    )
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function liked(Request $request)
    {
        $user = $this->loggedinUser;

        $request->merge([
            'user_id' => $user->id,
        ]);

        $request->merge([ 'with' => [
            'likable',
            'likable.user'
        ]]);


        $likable = $this->LikeI->models($request);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }
        
        if (!$likable['status']) {
            return $this->responseService->json('Fail!', [], 400, $likable['errors']);
        }

        $data = LikesListResource::collection($likable['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function likeHistory(Request $request)
    {
        $user = $this->loggedinUser;

        $request->merge([
            'user_id' => $user->id,
        ]);

        $request->merge([ 'with' => [
            'likable',
            // 'likable.user'
        ]]);


        $likable = $this->LikeI->models($request);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }
        
        if (!$likable['status']) {
            return $this->responseService->json('Fail!', [], 400, $likable['errors']);
        }

        $data = LikeHistoryListResource::collection($likable['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
