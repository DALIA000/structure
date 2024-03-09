<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Comment\CommentRepository;
use App\Http\Requests\Dashboard\AcceptCommentRequest;
use App\Http\Resources\Dashboard\CommentsListResource;
use \App\Http\Requests\Dashboard\DeleteReporeable;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Models\{
    Comment,
    Model,
};

class CommentController extends Controller
{
    public $loggedinUser;

    public function __construct(public CommentRepository $CommentI, public ResponseService $responseService){
        $this->loggedinUser = app('loggedinUser');
    }

    public function comments(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
        ], 'withCount' => [
        ]]);

        $models = $this->CommentI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CommentsListResource::collection($models['data']);

        return $this->responseService->json('Success!', $data, 200);
    }

    public function delete(DeleteReporeable $request, $id)
    {
        $model = $this->CommentI->deleteComment($request, $id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
