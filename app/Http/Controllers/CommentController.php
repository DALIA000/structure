<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Comment\CommentInterface;
use App\Http\Repositories\Like\LikeInterface;
use App\Http\Repositories\Report\ReportInterface;
use App\Http\Resources\CommentHistoryListResource;
use App\Http\Resources\CommentListResource;
use App\Http\Resources\LikesUsersListResource;
use App\Models\{
    Comment,
    Model
};
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\{
    ReportCommentRequest,
    CreateCommentRequest
};

class CommentController extends Controller
{
    private $loggedinUser;

    public function __construct(public CommentInterface $CommentI, public LikeInterface $LikeI, public ReportInterface $ReportI, public ResponseService $responseService) {
        $this->loggedinUser = app('loggedinUser');
    }

    public function commented(Request $request)
    {
        $user = $this->loggedinUser;

        $request->merge([
            'user_id' => $user->id,
        ]);

        $request->merge([ 'with' => [
            'commentable',
            'commentable.user'
        ]]);


        $commentable = $this->CommentI->models($request);
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }
        
        if (!$commentable['status']) {
            return $this->responseService->json('Fail!', [], 400, $commentable['errors']);
        }

        $data = CommentListResource::collection($commentable['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function commentHistory(Request $request)
    {
        $user = $this->loggedinUser;

        $request->merge([
            'user_id' => $user->id,
        ]);

        $request->merge([ 'with' => [
            'commentable',
            // 'commentable.user'
        ]]);


        $commentable = $this->CommentI->models($request);
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }
        
        if (!$commentable['status']) {
            return $this->responseService->json('Fail!', [], 400, $commentable['errors']);
        }

        $data = CommentHistoryListResource::collection($commentable['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateCommentRequest $request, Comment $comment)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $request->merge([
            'commentable_type' => get_class($comment),
        ]);

        $subComment = $this->CommentI->create($request, $user->id);

        return $this->responseService->json('Success!', $subComment, Response::HTTP_ACCEPTED);
    }

    public function comments(Request $request, $id)
    {
        $commentable = $this->CommentI->findById($id);
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $request->merge([
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);

        $request->merge([
            'withCount' => [
                'comments',
                'likes'
        ]]);

        $comments = $this->CommentI->models($request);

        if (!$comments) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$comments['status']) {
            return $this->responseService->json('Fail!', [], 400, $comments['errors']);
        }

        $data = CommentListResource::collection($comments['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function report(ReportCommentRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $reportable = $this->CommentI->findByWhere('id', $id, [], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$reportable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $request->merge([
            'reportable_id' => $reportable->id,
            'reportable_type' => get_class($reportable),
        ]);

        $report = $this->ReportI->create($request, $user->id);

        if (!$report) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$report['status']) {
            return $this->responseService->json('Fail!', [], 400, $report['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function comment(CreateCommentRequest $request, $id) // comment on comment
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $request->merge([
            'withCount' => [
                'comments',
                'likes'
            ]
        ]);

        $commentable = $this->CommentI->findByWith('id', $id, $request, [], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $request->merge([
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);

        $commetn = $this->CommentI->create($request, $user->id);

        if (!$commetn) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$commetn['status']) {
            return $this->responseService->json('Fail!', [], 400, $commetn['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function like(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $likable = $this->CommentI->findByWhere('id', $id, [], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $request->merge([
            'likable_id' => $likable->id,
            'likable_type' => get_class($likable),
        ]);

        $like = $this->LikeI->create($request, $user->id);

        if (!$like) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$like['status']) {
            return $this->responseService->json('Fail!', [], 400, $like['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unlike(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $likable = $this->CommentI->findById($id);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $model = Model::where('type', get_class($likable))->first();

        $request->merge([
            'likable_id' => $likable->id,
            'likable_type' => get_class($likable),
        ]);

        $like = $this->LikeI->unlike($request, $user->id);

        if (!$like) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$like['status']) {
            return $this->responseService->json('Fail!', [], 400, $like['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function likes(Request $request, $id)
    {
        $likable = $this->CommentI->findById($id);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $request->merge([
            'likable_id' => $likable->id,
            'likable_type' => get_class($likable),
        ]);

        $request->merge([
            'with' => [
                'user'
        ]]);

        $likes = $this->LikeI->models($request);

        if (!$likes) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$likes['status']) {
            return $this->responseService->json('Fail!', [], 400, $likes['errors']);
        }

        $data = LikesUsersListResource::collection($likes['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $comment = $user->comments()->where('id', $id)->first();

        if (!$comment) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $delete = $this->CommentI->delete($comment->id);

        if (!$delete) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}