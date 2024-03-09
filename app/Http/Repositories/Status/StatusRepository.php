<?php

namespace App\Http\Repositories\Status;

use App\Http\Repositories\Status\StatusInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Status;
use DB;
use Str;

class StatusRepository extends BaseRepository implements StatusInterface
{
    public $loggedinUser;

    public function __construct(Status $model)
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

            if ($request->exists('slug') && $request->slug !== null) {
                $query->where('slug', $request->slug);
            }
            
            if ($request->exists('model') && $request->model !== null) {
                $query->whereHas('model_status.model', function ($query) use ($request) {
                    $query->where('slug', $request->model);
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
