<?php

namespace App\Http\Controllers;

use App\Http\Requests\{
    CreateCommentRequest,
    CreateVideoRequest,
    ReportVideoRequest,
    PromoteVideoRequest
};
use App\Models\{
    Model,
    Video
};
use App\Http\Repositories\View\ViewInterface;
use Illuminate\Http\Request;
use App\Http\Repositories\{
    Like\LikeInterface,
    Report\ReportInterface,
    Save\SaveInterface,
    Promote\PromoteInterface,
    Share\ShareInterface,
    Status\StatusInterface,
    Video\VideoInterface,
    User\UserInterface,
    Comment\CommentInterface
};
use App\Services\ResponseService;
use App\Http\Resources\{
    LikesUsersListResource,
    VideosListResource,
    CommentListResource,
    PromotedVideosListResourse
};
use Carbon\Carbon;

class VideoController extends Controller
{
    public $VideoI;
    public $loggedinUser;

    public function __construct(VideoInterface $VideoI, public SaveInterface $SaveI, public PromoteInterface $PromoteI, public ResponseService $responseService, public CommentInterface $CommentI, public LikeInterface $LikeI, public StatusInterface $StatusI, public UserInterface $UserI, public ViewInterface $ViewI, public ShareInterface $ShareI, public ReportInterface $ReportI)    {
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

        $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'status_id' => $status_id,
        ]);

        $request->merge(['with' => [
            'user',
            'course'
        ], 'withCount' => [
            'likes',
            'has_liked',
            'comments',
            'views',
            'shares'
        ]]);

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

    public function teams(Request $request)
    {
        $request->merge([
            'teams' => 1,
            'order' => 'desc',
            'sort' => 'likes_count',
        ]);

        return $this->videos($request);
    }

    public function followingsVideos(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'status_id' => $status_id,
            'followings' => 1,
        ]);

        $request->merge(['with' => [
            'user',
            'course'
        ], 'withCount' => [
            'likes',
            'has_liked',
            'comments',
            'views',
            'shares'
        ]]);

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

    public function drafts(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $status_id = $this->StatusI->findBySlug('draft')?->id;

        $request->merge([
            'status_id' => $status_id,
            'user_id' => $this->loggedinUser->id,
        ]);

        $request->merge(['with' => [
            'user',
        ], 'withCount' => [
        ]]);

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

    public function video(Request $request, $id)
    {
        $request->merge([
            'id' => $id,
        ]);

        $request->merge(['with' => [
            'user',
        ], 'withCount' => [
            'likes',
            'comments',
            'views',
            'shares'
        ]]);

        $models = $this->VideoI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $model = $models['data']?->first();
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound')]]);
        }

        $data = new VideosListResource($model);

        return $this->responseService->json('Success!', ['video' => $data], 200);
    }

    public function activate(Request $request, $id)
    {
        $request->merge(['with' => [
        ], 'withCount' => [
        ]]);

        $model = $this->VideoI->findById($id);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->VideoI?->model])]]);
        }

        if ($model->user_id != $this->loggedinUser->id) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->VideoI?->model])]]);
        }

        if ($model->status_id !== 7) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.update', ['model' => $this->VideoI?->model])]]);
        }

        $model->update(['status_id' => 1]);
        $data = $model;

        return $this->responseService->json('Success!', ['video' => $data->only('id')], 200);
    }

    public function create(CreateVideoRequest $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'user_id' => $user->id,
            'status_id' => $status_id,
        ]);

        $video = $this->VideoI->create($request);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        $data = $video['data'];
        return $this->responseService->json('Success!', $data->only('id'), 200);
    }

    public function like(Request $request, $id)
    {
        $likable = $this->VideoI->findByWhere('id', $id, ['status_id' => 1], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });

        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
        }

        $request->merge([
            'likable_id' => $likable?->id,
            'likable_type' => get_class($likable),
        ]);

        $like = $this->LikeI->create($request);

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

        $likable = $this->VideoI->findById($id);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }

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

    public function comments(Request $request, $id)
    {
        $request->merge([
            'withCount' => [
                'comments',
                'likes'
        ]]);
        $commentable = $this->VideoI->findByWith('id', $id, $request, [], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
        }

        $request->merge([
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);

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

    public function likes(Request $request, $id)
    {
        $likable = $this->VideoI->findById($id);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
        }

        $request->merge([
            'likable_id' => $likable?->id,
            'likable_type' => get_class($likable),
        ]);

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

    public function comment(CreateCommentRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $commentable = $this->VideoI->findByWhere('id', $id, ['status_id' => 1], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
        }

        $request->merge([
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);

        $comment = $this->CommentI->create($request, $user->id);

        if (!$comment) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$comment['status']) {
            return $this->responseService->json('Fail!', [], 400, $comment['errors']);
        }

        $data = $comment['data'];
        return $this->responseService->json('Success!', $data->only('id'), 200);
    }

    public function deleteComment(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $comment = $this->CommentI->findById($id);
        if (!$comment) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $model = Model::where('type', get_class($comment))->first();


        $request->merge([
            'comment_id' => $comment->id,
        ]);

        $comment = $this->CommentI->deleteComment($request, $user->id);

        if (!$comment) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$comment['status']) {
            return $this->responseService->json('Fail!', [], 400, $comment['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function view(Request $request, $id)
    {
        $request->merge([
            'ip' => $request->ip(),
            'viewable_id' => $id,
            'viewable_type' => Video::class,
        ]);

        $video = $this->ViewI->create($request);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function share(Request $request, $id)
    {
        $request->merge([
            'ip' => $request->ip(),
            'model_id' => $id,
            'model_type' => get_class($this->VideoI->model),
        ]);

        $video = $this->ShareI->create($request);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function report(ReportVideoRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $reportable = $this->VideoI->findByWhere('id', $id, ['status_id' => 1], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$reportable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
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

    public function save(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $savable = $this->VideoI->findByWhere('id', $id, ['status_id' => 1], function ($query) use ($request) {
                $query->whereHas('user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$savable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
        }

        $request->merge([
            'savable_id' => $request->id,
            'savable_type' => get_class($savable),
        ]);

        $save = $this->SaveI->create($request, $user->id);

        if (!$save) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$save['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $save['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unsave(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $savable = $this->VideoI->findByid($id);
        if (!$savable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }

        $request->merge([
            'savable_id' => $savable->id,
            'savable_type' => get_class($savable),
        ]);

        $save = $this->SaveI->unsave($request, $user->id);

        if (!$save) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$save['status']) {
            return $this->responseService->json('Fail!', [], 400, $save['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function promote(PromoteVideoRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $promotable = $this->VideoI->findByWhere('id', $id, ['status_id' => 1, 'user_id' => $user->id]);
        if (!$promotable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->VideoI->model)])]);
        }

        $request->merge([
            'promotable_id' => $request->id,
            'promotable_type' => get_class($promotable),
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays($request->duration),
            'target' => [
                'user_types' => $request->user_types,
                'cities' => $request->cities
            ]
        ]);

        $promote = $this->PromoteI->create($request, $user->id);

        if (!$promote) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$promote['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $promote['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function promotes(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $model = Model::where('type', Video::class)->first();

        $request->merge([
            'user_id' => $this->loggedinUser?->id,
            'model_type' => $model->type,
        ]);

        $models = $this->PromoteI->models($request);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = PromotedVideosListResourse::collection($models['data']);

        return $this->responseService->json('Success!', ['promotes' => $data], 200, paginate: 'promotes');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->VideoI->deleteVideo($request, $id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!',[] , 200);
    }
}
