<?php

namespace App\Http\Repositories\Preference;

use App\Http\Repositories\Base\BaseRepository;
use App\Models\Preference;

class PreferenceRepository extends BaseRepository implements PreferenceInterface
{
    public function __construct(Preference $model)
    {
        $this->model = $model;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('id') && $request->id !== null) {
                $query->where('id', $request->id);
            }

            if ($request->exists('is_professional_specific') && $request->is_professional_specific !== null) {
                $query->where('is_professional_specific', $request->is_professional_specific);
            }

            if ($request->exists('user_type') && $request->user_type !== null) {
                $query->whereHas('user_type_preferences.user_type', function ($query) use ($request) {
                    $query->where('id', $request->user_type);
                });
            }
        });

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }
}
