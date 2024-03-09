<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\Dashboard\AcademyLevelResource;
use App\Http\Requests\Dashboard\StoreAcademyLevelRequest;
use App\Http\Requests\Dashboard\UpdateAcademyLevelRequest;
use App\Http\Repositories\AcademyLevel\AcademyLevelInterface;

class AcademyLevelController extends Controller
{

    public function __construct(private AcademyLevelInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function findById($id)
    {
        $model = cacheGet('academy_levels_' . $id);
        if (!$model) {
            $model = $this->modelInterface->findById($id);
            if (!$model) {
                return responseJson(404, __('messages.data not found'));
            } else {
                cachePut('academy_levels_' . $id, $model);
            }
        }

        return responseJson(200, 'success', new AcademyLevelResource($model));
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('academy_levels');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('academy_levels', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', AcademyLevelResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StoreAcademyLevelRequest$request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(UpdateAcademyLevelRequest$request, $id)
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
