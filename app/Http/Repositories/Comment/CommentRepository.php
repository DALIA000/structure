<?php

namespace App\Http\Repositories\Comment;

use App\Events\DeleteReportable;
use App\Events\UserCommented;
use App\Http\Repositories\Comment\CommentInterface;
use App\Http\Repositories\Status\StatusInterface;

use App\Http\Repositories\Base\BaseRepository;
use App\Http\Repositories\User\UserInterface;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Model;
use App\Models\Report;

class CommentRepository extends BaseRepository implements CommentInterface
{
    public $loggedinUser;

    public function __construct(Comment $model, private StatusInterface $StatusI, public UserInterface $UserI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('user_id') && $request->user_id !== null) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->exists('commentable_id') && $request->commentable_id !== null && $request->exists('commentable_type') && $request->commentable_type !== null) {
                $query->where([
                    'commentable_id' => $request->commentable_id,
                    'commentable_type' => $request->commentable_type,
                ]);
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?? []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function commentHistory($request)
    {
        $user = app('loggedinUser');

        $comments = $request->per_page ? $user->comment()->paginate($request->per_page) : $user->comment()->get();

        return $comments;
    }

    public function create($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->comments()->create([
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'comment' => $request->comment,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        if (Model::find($model->model_id)?->type !== Blog::class) {
            UserCommented::dispatch($model);
        }

        return ['status' => true, 'data' => $model];
    }

    public function setStatus($model, $status)
    {
        return $model->update([
            'status_id' => $status,
        ]);
    }

    public function deleteComment($request, $id)
    {
        if($this->loggedinUser instanceof Admin) {
            $model = $this->findByWhere('id', $id);
        }else{
            $model = $this->findByWhere('id', $id, ['user_id' => $this->loggedinUser?->id]);
        }
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $this->delete($id);
        DeleteReportable::dispatch($model, $request->note);
        return ['status' => true, 'data' => []];
    }
}
