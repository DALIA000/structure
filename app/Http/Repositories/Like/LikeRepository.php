<?php

namespace App\Http\Repositories\Like;

use App\Events\UserLikedVideo;
use App\Http\Repositories\Like\LikeInterface;
use App\Http\Repositories\Status\StatusInterface;

use App\Http\Repositories\Base\BaseRepository;
use App\Http\Repositories\User\UserInterface;
use App\Models\Like;
use App\Models\Video;
use App\Models\Comment;
use App\Models\Blog;

class LikeRepository extends BaseRepository implements LikeInterface
{
    public $loggedinUser;

    public function __construct(Like $model, private StatusInterface $StatusI, public UserInterface $UserI)
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
            
            if ($request->exists('likable_id') && $request->likable_id !== null) {
                $query->where('likable_id', $request->likable_id);
            }

            if ($request->exists('type') && $request->type !== null) {
                switch ($request->type) {
                    case '1':
                        $query->where('likable_type', Video::class);
                        break;
                    case '2':
                        $query->where('likable_type', Blog::class);
                        break;
                    case '4':
                        $query->where('likable_type', Comment::class);
                        break;
                    
                    default:
                        $query->where('likable_type', Video::class);
                        break;
                }
            }

            if ($request->exists('model_type') && $request->model_type !== null) {
                $query->where([
                    'likable_id' => $request->model_id,
                    'likable_type' => $request->model_type,
                ]);
            }
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models->with($request->with ?? [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request) // like
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->likes()->updateOrCreate([
            'user_id' => $user->id,
            'likable_id' => $request->likable_id,
            'likable_type' => $request->likable_type,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->wasRecentlyCreated) {
            UserLikedVideo::dispatch($model);
        }

        return ['status' => true, 'data' => $model];
    }

    public function unlike($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->likes()->where([
            'likable_id' => $request->likable_id,
            'likable_type' => $request->likable_type,
        ])->first();

        if ($model) {
            $model->delete();
        }

        return ['status' => true, 'data' => []];
    }

    public function setStatus($model, $status)
    {
        return $model->update([
            'status_id' => $status,
        ]);
    }
}
