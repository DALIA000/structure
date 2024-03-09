<?php

namespace App\Http\Repositories\Tag;

use App\Models\Tag;

class TagRepository implements TagInterface
{
    public function __construct(private Tag $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function all($request)
    {
        $models = $this->model->where(function ($q) use ($request) {
            if ($request->search) {
                $q->where('name', 'like', "%{$request->search}%");
            }

        })->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create($request)
    {
        $model = $this->model->create([
            'name' => $request->name,
        ]);
        cacheForget('tags');

    }

    public function update($request, $id)
    {
        if ($request->name) {
            $this->clear($id);
            $model = $this->model->find($id);
            $model->name = $request->name;
            $model->save();
        }

    }

    public function destroy($id)
    {
        $model = $this->findById($id);
        $this->clear($id);
        $model->delete();
    }

    private function clear($id)
    {
        cacheForget('tags_' . $id);
        cacheForget('tags');
    }
}
