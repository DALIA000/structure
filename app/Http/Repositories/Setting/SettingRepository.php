<?php

namespace App\Http\Repositories\Setting;

use App\Http\Repositories\Base\BaseRepository;
use App\Models\Setting;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SettingRepository extends BaseRepository implements SettingInterface
{
    public function __construct(Setting $model, private Media $media)
    {
        $this->model = $model;
    }

    public function updateValue($request)
    {
        $items = array_filter($request->all());

        foreach ($items as $key => $value) {
            $this->model->where('slug', $key)->update(['value' => $value]);
        }

        return true;
    }

    public function update($request, $slug)
    {
        $model = $this->findBySlug($slug);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (isset($request->locales)) {
            setLocales($this->model, $request->locales, $model->id);
        }

        if ($slug == "about-us") {
            if ($request->media && !$request->old_media) {
                $model->clearMediaCollection('media');
                foreach ($request->media as $media) {
                    uploadImage($media, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if ($request->old_media && !$request->media) {
                $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                    $media->delete();
                });
            }

            if ($request->old_media && $request->media) {
                $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                    $media->delete();
                });
                foreach ($request->media as $image) {
                    uploadImage($image, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }
        } else {
            if (isset($request->media)) {
                $this->media::where('id', $model->media[0]->id)->delete();
                $this->media::where('id', $request->media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }

            if (isset($request['media'])) {
                $model->clearMediaCollection('media');
                uploadImage($request['media'], [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        return ['status' => true, 'data' => $model];
    }

    public function updateImage($slug, $request)
    {
        $model = $this->model->where('slug', $slug)->first();

        $this->media::where('id', $model->files[0]->id)->delete();
        $this->media::where('id', $request->$slug)->update([
            'model_id' => $model->id,
            'model_type' => get_class($this->model),
        ]);

        return true;
    }

    public function updateWithSlug($slug, $request)
    {
        $model = $this->findBySlug($slug);
        setLocales($this->model, $request->$slug, $model->id);

    }
}
