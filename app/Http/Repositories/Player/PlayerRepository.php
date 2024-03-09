<?php

namespace App\Http\Repositories\Player;

use App\Events\UserAccepted;
use App\Events\UserRejected;
use App\Http\Repositories\Player\PlayerInterface;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\{
    Player,
    AcademyPlayer,
    User
};

class PlayerRepository extends BaseRepository implements PlayerInterface
{
    public $loggedinUser;

    public function __construct(Player $model, public User $user, private StatusInterface $StatusI, private AcademyPlayer $AcademyPlayer)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function findByUsername($username, $request = null)
    {
        $model = $this->user->where('username', $username)
            ->where(['user_type_class' => $this->model::class])
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
                    $query->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%")
                            ->orWhereHas('account', function ($query) use ($request) {
                                $query->where('email', 'like', "%{$request->search}%")
                                        ->orWhere('username', 'like', "%{$request->search}%");
                            });
                });
            }

            if ($request->exists('email') && $request->email !== null) {
                $query->whereHas('account', function ($query) use ($request) {
                    $query->where('email', 'like', "%{$request->email}%");
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

            // academy username
            if ($request->exists('academy') && $request->academy !== null) {
                $query->whereHas('academy_player', function ($query) use ($request) {
                    $query->where('status_id', 1);
                    $query->whereHas('academy.account', function ($query) use ($request) {
                        $query->where('username', $request->academy);
                    });
                });
            }

            if ($request->exists('academy_id') && $request->academy_id !== null) {
                $query->whereHas('academy_player', function ($query) use ($request) {
                    $query->where('academy_id', $request->academy_id);
                });
            }

            if ($request->exists('player_position_id') && $request->player_position_id !== null) {
                $query->where('player_position_id', $request->player_position_id);
            }

            if ($request->exists('player_footness_id') && $request->player_footness_id !== null) {
                $query->where('player_footness_id', $request->player_footness_id);
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

    public function link($request, $player, $academy)
    {
        $model = AcademyPlayer::updateOrCreate([
            'academy_id' => $academy,
            'player_id' => $player,
        ],
        [
            'status_id' => 1, // active
            'strength_points' => $request->strength_points,
        ]);

        return ['status' => true, 'data' => $model];
    }

    public function unlink($request, $player, $academy)
    {
        AcademyPlayer::where([
            'academy_id' => $academy,
            'player_id' => $player,
        ])->forceDelete();

        return ['status' => true, 'data' => []];
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
