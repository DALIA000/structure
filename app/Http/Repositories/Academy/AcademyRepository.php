<?php

namespace App\Http\Repositories\Academy;

use App\Events\UserAccepted;
use App\Events\UserRejected;
use App\Http\Repositories\Academy\AcademyInterface;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\{
    Academy,
    AcademyPlayer,
    User
};

class AcademyRepository extends BaseRepository implements AcademyInterface
{
    public $loggedinUser;

    public function __construct(Academy $model, public User $user, public AcademyPlayer $academyPlayer, private StatusInterface $StatusI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function findByUsername($username, $request=null)
    {
        $model = $this->user->where('username', $username)
            ->where(['user_type_class' => get_class($this->model)])
            ->whereNot(function ($query) use ($request) {
                $query->whereHas('blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            })
            ->first();
        return $model ?? false;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('business_name', 'like', "%{$request->search}%");
                });
            }
            
            if ($request->exists('city') && $request->city !== null) {
                $query->whereHas('account', function ($query) use ($request) {
                    $query->where('city_id', $request->city);
                });
            }
            
            if ($request->exists('status') && $request->status !== null) {
                $query->whereHas('status', function ($query) use ($request) {
                    $query->where('slug', $request->status);
                });
            }
            
            if ($request->exists('academy_level') && $request->academy_level !== null) {
                $query->whereHas('academy_level', function ($query) use ($request) {
                    $query->where('id', $request->academy_level);
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('account.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('account.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function request_link($request, $player, $academy)
    {
        $model = AcademyPlayer::firstOrCreate(
            [
                'academy_id' => $academy,
                'player_id' => $player,
            ],
            [
                'status_id' => 2, // pending
            ]
        );

        return ['status' => true, 'data' => $model];
    }
    
    public function accept($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $this->acceptUser($model);

        if ($model){
            UserAccepted::dispatch($model->account);
        }

        return ['status' => true, 'data' => $model?->account];
    }
    
    public function reject($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('rejected')?->id;
        if ($status_id !== $model->status_id){
            $this->setStatus($model, $status_id);
            UserRejected::dispatch($model->account, $request->note);
        }

        return ['status' => true, 'data' => $model?->account];
    }

    public function block($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('blocked')?->id;
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function unblock($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('active')?->id;
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function setStatus($model, $status)
    {
        return $model->update([
            'status_id' => $status,
        ]);
    }
}
