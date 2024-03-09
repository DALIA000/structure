<?php

namespace App\Http\Repositories\Base;

use App\Http\Repositories\Base\BaseInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Country;
use App\Models\Image;
use File;
use Auth;
use Str;

class BaseRepository implements BaseInterface
{
    public function __construct(public Model $model)
    {
    }

    public function findBySlug($slug)
    {
        $model = $this->model->where('slug', $slug)->first();
        return $model ?? false;
    }

    public function findBySlugPulk($slugs)
    {
        $models = $this->model->whereIn('slug', $slugs)->get();
        return $models;
    }

    public function findByUserTypePulk($user_types)
    {
        $models = $this->model->whereIn('user_type', $user_types)->get();
        return $models;
    }

    public function findById($id)
    {
        $model = $this->model->find($id);
        return $model ?? false;
    }

    public function findWhere($where)
    {
        $model = $this->model->where($where)->first();
        return $model ?? false;
    }

    public function findByWhere($column, $value, $where = [], $whereNot = [])
    {
        $model = $this->model->where($column, $value)->where($where)->whereNot($whereNot)->first();
        return $model ?? false;
    }

    public function  findByWith($column, $value, $request, $where = [], $whereNot = [])
	{
		$model = $this->model->where($column, $value)->with($request->with??[])->withCount($request->withCount??[])->where($where)->whereNot($whereNot)->first();
		return $model ?? false;
	}

    public function findByIdWith($request)
	{
		$model = $this->model->where('id', $request->id)->with($request->with??[])->withCount($request->withCount??[])->first();
		return $model ?? false;
	}
    
    public function findByIdPulk($ids)
    {
        $models = $this->model->whereIn('id', $ids)->get();
        return $models;
    }

    public function findTrashedById($id)
    {
        $model = $this->model->withTrashed()->find($id);
        return $model ?? false;
    }

    public function delete($id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (($model instanceof Admin && $model->id == 1) || ($model instanceof Role && $model->id == 1) || ($model instanceof Country && $model->id == 1)) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.protected', ['model' => trans_class_basename($this->model)])]]];
        }

        if (isset($this->model::$cascade)) {
            foreach ($this->model::$cascade as $cascade) {
                if ($model->$cascade && $count = $model->$cascade->count()) {
                    return ['status' => false, 'errors' => ['error' => [trans('auth.cascade_delete', ['model' => trans_class_basename($model), 'count' => $count, 'cascade' => trans('models.' . $cascade)])]]];
                }
            }
        }
        $model->delete();
        return ['status' => true, 'data' => []];
    }

    public function deletePulk($ids)
    {
        $models = $this->model->whereIn('id', $ids);
        if (!$models) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        foreach ($ids as $id) {
            $model = $this->delete($id);
            if (!$model || !$model['status']) {
                return $model;
            }
        }
        return ['status' => true, 'data' => []];
    }

    public function restore($request, $id)
    {
        $model = $this->findTrashedById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->restore();
        $model->locales()->withTrashed()->restore();

        if (method_exists($model, 'images')) {
            $model->images()->withTrashed()->restore();
        } elseif (method_exists($model, 'image')) {
            $model->image()->withTrashed()->restore();
        }

        // $model->status = 0;

        $model->save();
        $model->refresh();
        return ['status' => true, 'data' => $model];
    }

    public function forceDelete($id)
    {
        $model = $this->findTrashedById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (
            ($model instanceof Admin && $model->id == 1) ||
            ($model instanceof Role && $model->id == 1) ||
            ($model instanceof Country && $model->id == 1)
        ) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.protected', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model instanceof Admin && $model->id == $this->loggedinUser->id) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.forbidden', ['model' => trans_class_basename($this->model)])]]];
        }

        if (isset($this->model::$cascade)) {
            foreach ($this->model::$cascade as $cascade) {
                if ($model->$cascade && $count = $model->$cascade->count()) {
                    return ['status' => false, 'errors' => ['error' => 'this model is assigned to ' . $count .' ' . $cascade . ' ,if you are sure to remove it, please remove them first.' ]];
                }
            }
        }

        if (isset($this->model::$files)) {
            foreach ($this->model::$files as $file) {
                File::delete($model->$file);
            }
        }

        $model->locales ? $model->locales()->forceDelete() : null;
        $model->forceDelete();
        return ['status' => true, 'data' => []];
    }

    public function setLocales($model, $locales, $slug=false, $generatedSlug=null)
    {
        foreach ($locales as $lang => $locale) {
            if ($slug && $lang && $lang == 'en' && $locale['name']) {
                switch (get_class($model)) {
                    case Role::class:
                        $model->update([
                          'name' => $generatedSlug ? $generatedSlug : Str::slug($locale['name']),
                        ]);
                        break;

                    default:
                        $model->update([
                          'slug' => $generatedSlug ? $generatedSlug : Str::slug($locale['name']),
                        ]);
                        break;
                }
            }

            $model->locales()->updateOrCreate(['locale' => $lang], $locale);
        }
    }

  public function setSocials($model, $socials)
  {
      $model->socials()->updateOrCreate(['socialable_id' => $model->id], $socials);
  }

    public function setSortParams($request)
    {
        switch ($request->sort) {
            default:
                $sort = $request->sort ?: 'created_at';
                break;
        }

        switch ($request->order) {
            default:
                $order = $request->order ?: 'desc';
                break;
        }

        return [$sort, $order];
    }

  public function getWith($models, $with)
  {
      return $models->with($with);
  }

  public function getWithCount($models, $withCount)
  {
      return $models->withCount($withCount);
  }

  public function generateSlug($model, $name, $column='slug')
  {
      $slug = Str::slug($name);
      $count = 1;

      while ($model->where($column, $slug)->get()->count()) {
          $slug = Str::slug($name) . '-' . $count++;
      }
      return $slug;
  }

  public function setImages($model, $images)
  {
      $images = collect($images)->reject(fn ($image) => is_null($image))->toArray();

      $model_images = $model->images;
      if ($model->images) {
          foreach ($model_images as $image) {
              if (!in_array($image->id, $images)) {
                  File::delete($image?->image_url);
                  $image->forceDelete();
              }
          }
      }

      $model->refresh();

      $model_images_id = $model_images ? $model_images->pluck('id')->toArray() : [];
      $images = array_diff($images, $model_images_id);

      if (count($images)) {
          Image::whereIn('id', $images)->update(['imagable_id' => $model->id, 'imagable_type' => get_class($model)]);
          $model->refresh();
          // return true;
      }
  }

    public function editPulk($ids, $status)
    {
        $models = $this->model->whereIn('id', $ids);

        if (!$models) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $models->update(['status' => $status]);

        return ['status' => true, 'data' => $models->get()];
    }

    public function acceptUser($model)
    {
        $model = \DB::transaction(function () use ($model) {
            $account = $model->account;

            $account->user()->delete();
            // delete old user preferences
            $old_preferences = $account->user_preferences;
            $account->user_preferences()->delete();

            $account->update([
                'user_type_class' => get_class($this->model),
            ]);

            $model->update(['status_id' => 1]);

            // set new preferences
            $preferences = $account->user_type->preferences->each(fn($i)=> $account->user_preferences()->create([
                'preference_id' => $i->id,
                'value' => $old_preferences->where('id', $i->id)?->first()?->value ?: 1,
            ]));

            return $model;
        });

        return $model;
    }
}
