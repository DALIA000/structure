<?php

namespace App\Http\Repositories\TrainerExperienceLevel;

use App\Models\TrainerExperienceLevel;
use App\Http\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class TrainerExperienceLevelRepository extends BaseRepository implements TrainerExperienceLevelInterface
{
    public function __construct(TrainerExperienceLevel $model)
    {
        $this->model = $model;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('id') && $request->id !== null) {
                $query->where('id', $request->id);
            }
        });

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function findById($id)
    {
        return $this->model->where('id', $id)->with(['locales'])->first();
    }

    public function all($request)
    {
        $models = $this->model->where(function ($q) use ($request) {
            if ($request->search) {
                $q->whereHas('locales', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%");
                });
            }
        })->with(['locales'])->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $model = $this->model->create();

            if ($request->locales) {
                setLocales($this->model, $request->locales, $model->id);
            }

            $this->forget($model->id);
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $model = $this->model->find($id);

            if ($request->locales) {
                setLocales($this->model, $request->locales, $model->id);
            }

            $this->forget($id);
        });
    }

    public function destroy($id)
    {
        $model = $this->findById($id);
        $this->forget($id);
        $model->delete();
    }

    private function forget($id)
    {
        $keys = [
            "trainer_experience_levels",
            "trainer_experience_levels_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
