<?php

namespace App\Http\Repositories\AcademyPlayer;

use App\Http\Repositories\AcademyPlayer\AcademyPlayerInterface;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\{
    AcademyPlayer,
    Academy,
    User
};

class AcademyPlayerRepository extends BaseRepository implements AcademyPlayerInterface
{
    public $loggedinUser;

    public function __construct(AcademyPlayer $model, public User $user, public Academy $academyPlayer, private StatusInterface $StatusI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('status') && $request->status !== null) {
                $query->whereHas('status', function ($query) use ($request) {
                    $query->where('slug', $request->status);
                });
            }

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where('status_id', $request->status_id);
            }

            if ($request->exists('academy_id') && $request->academy_id !== null) {
                $query->where('academy_id', $request->academy_id);
            }

            if ($request->exists('player_id') && $request->player_id !== null) {
                $query->where('player_id', $request->player_id);
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('player.account.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('player.account.blocked', function ($query) use ($request) {
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
}
