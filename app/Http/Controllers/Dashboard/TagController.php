<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Repositories\Tag\TagInterface;
use App\Http\Requests\Dashboard\StoreTagRequest;
use App\Http\Requests\Dashboard\UpdateTagRequest;
use App\Http\Resources\Dashboard\TagResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TagController extends Controller
{
    private $modelInterface;

    public function __construct(TagInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function findById($id)
    {
        $model = cacheGet('tags_' . $id);
        if (!$model) {
            $model = $this->modelInterface->findById($id);
            if (!$model) {
                return responseJson(404, __('messages.data not found'));
            } else {
                cachePut('tags_' . $id, $model);
            }
        }

        return responseJson(200, 'success', new TagResource($model));
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('tags');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('tags', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', TagResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StoreTagRequest $request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $model = $this->modelInterface->findById($id);
        if (!$model) {
            return responseJson(404, __('messages.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function destroy($id)
    {
        $model = $this->modelInterface->findById($id);
        if (!$model) {
            return responseJson(404, __('messages.data not found'));
        }

        $this->modelInterface->destroy($id);

        return responseJson(200, 'success');
    }

}
