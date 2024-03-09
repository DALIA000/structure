<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

function trans_class_basename($class) {
  return trans('models.'.class_basename($class));
}

function remove_special_chars($str) {
  return str_replace(['\'', '"', ',' , ';', '<', '>', '(', ')', '-', '+'], '', $str);
}






/**
 * @param message which return with of response json
 * @param  data  $object
 * @param response_status like 200,500,400
 * @param  pagination  $pagination have default value null
 * @return response json
 */
function responseJson($response_status, $massage, $object = null, $pagination = null)
{
    return response()->json([
        'message' => $massage,
        'data' => $object,
        'pagination' => $pagination,
    ], $response_status);
}

/**
 * @param collection of data resource
 * @return array of properties for pagination
 */
function getPaginates($collection)
{
    return [
        'per_page' => $collection->perPage(),
        'path' => $collection->path(),
        'total' => $collection->total(),
        'current_page' => $collection->currentPage(),
        'next_page_url' => $collection->nextPageUrl(),
        'previous_page_url' => $collection->previousPageUrl(),
        'last_page' => $collection->lastPage(),
        'has_more_pages' => $collection->hasMorePages(),
    ];
}

/**
 * @param model which you want to relation with it
 * @param relation_name
 */
function setLocales(Model $model, $locales, $id)
{
    $model = $model->find($id);

    foreach ($locales as $locale => $value) {
        $case = $model->locales()->where('locale', $locale)->first();
        if ($case) {
            $case->update($value);
        } else {
            $model->locales()->create(['locale' => $locale]);
            $model->locales()->where('locale', $locale)->update($value);
        }
    }
}

function user()
{
    return \App\Services\LoggedinUser::user();
}


// Cache methods
function cachePut($key, $value, $minutes = null)
{
    if ($minutes) {
        return Cache::put($key, $value, $minutes);
    } else {
        return Cache::put($key, $value);
    }
}

function cacheGet($key)
{
    return Cache::get($key);
}

function cacheForget($key)
{
    return Cache::forget($key);
}
/**
 * @param id the id of media which i will updated it
 * @param value expected array from it
 */
function uploadImage($id, array $value)
{
    \Spatie\MediaLibrary\MediaCollections\Models\Media::where('id', $id)->update($value);
}
