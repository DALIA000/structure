<?php

namespace App\Http\Repositories\SpamSection;

use App\Http\Repositories\SpamSection\SpamSectionInterface;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\SpamSection;

class SpamSectionRepository extends BaseRepository implements SpamSectionInterface
{
    public $loggedinUser;

    public function __construct(SpamSection $model, private StatusInterface $StatusI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('slug', 'like', '%'.$request->search.'%')
                          ->orWhereHas('locales', function ($query) use ($request) {
                              $query->where('name', 'like', '%'.$request->search.'%');
                          });
                });
            }
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
