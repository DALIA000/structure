<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Repositories\Comment\CommentInterface;
use App\Http\Repositories\Like\LikeInterface;
use App\Http\Repositories\Save\SaveRepository;
use App\Http\Repositories\View\ViewInterface;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Resources\CommentListResource;
use App\Http\Resources\LikesUsersListResource;
use App\Models\Model;
use Illuminate\Http\Request;
use App\Http\Repositories\Blog\BlogInterface;
use App\Http\Resources\BlogResource;
use App\Http\Resources\BlogsListResource;
use App\Services\ResponseService;

class BlogController extends Controller
{
    public $loggedinUser;

    public function __construct(public BlogInterface $BlogI, public SaveRepository $SaveI, public ResponseService $responseService, public LikeInterface $LikeI, public CommentInterface $CommentI, public ViewInterface $ViewI)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function blogs(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'locales',
            'media',
            'tags',
        ], 'withCount' => [
            'likes',
            'comments',
            'views',
        ]]);

        $request->merge([
            'status_id' => 1,
        ]);

        $models = $this->BlogI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = BlogsListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['blogs' => $data], 200, paginate: 'blogs');
    }

    public function blog(Request $request, $id)
    {
        $request->merge(['with' => [
            'locales',
            'media',
            'tags',
        ], 'withCount' => [
            'likes',
            'comments',
            'views',
        ]]);

        $request->merge([
            'status' => 'active',
        ]);

        $model = $this->BlogI->findByIdWith($request);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        $data = new BlogResource($model);

        return $this->responseService->json('Success!', ['blogs' => $data], 200, paginate: 'blogs');
    }

    public function like(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $likable = $this->BlogI->findById($id);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->BlogI->model)])]);
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

        $likable = $this->BlogI->findById($id);
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

    public function likes(Request $request, $id)
    {
        $likable = $this->BlogI->findById($id);
        if (!$likable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->BlogI->model)])]);
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

    public function comments(Request $request, $id)
    {
        $commentable = $this->BlogI->findById($id);
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->BlogI->model)])]);
        }

        $request->merge([
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);

        $request->merge(['withCount' => [
            'comments',
            'likes',
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

    public function comment(CreateCommentRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $commentable = $this->BlogI->findById($id);
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->BlogI->model)])]);
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

        $commentable = $this->CommentI->findById($id);
        if (!$commentable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CommentI->model)])]);
        }

        $request->merge([
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
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
            'model_id' => $id,
            'model_type' => get_class($this->BlogI->model),
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
            'model_type' => get_class($this->BlogI->model),
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

    public function save(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $savable = $this->BlogI->findByWhere('id', $id, ['status_id' => 1]);
        if (!$savable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->BlogI->model)])]);
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

        $savable = $this->BlogI->findById($id);
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
}
