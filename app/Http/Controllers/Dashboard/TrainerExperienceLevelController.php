<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\Dashboard\TrainerExperienceLevelResource;
use App\Http\Requests\Dashboard\StoreTrainerExperienceLevelRequest;
use App\Http\Requests\Dashboard\UpdateTrainerExperienceLevelRequest;
use App\Http\Repositories\TrainerExperienceLevel\TrainerExperienceLevelInterface;

class TrainerExperienceLevelController extends Controller
{

    public function __construct(private TrainerExperienceLevelInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function findById($id)
    {
        $model = cacheGet('trainer_experience_levels' . $id);
        if (!$model) {
            $model = $this->modelInterface->findById($id);
            if (!$model) {
                return responseJson(404, __('messages.data not found'));
            } else {
                cachePut('trainer_experience_levels' . $id, $model);
            }
        }

        return responseJson(200, 'success', new TrainerExperienceLevelResource($model));
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('trainer_experience_levels');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('trainer_experience_levels', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', TrainerExperienceLevelResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StoreTrainerExperienceLevelRequest$request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(UpdateTrainerExperienceLevelRequest$request, $id)
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
