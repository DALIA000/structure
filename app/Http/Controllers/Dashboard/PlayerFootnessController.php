<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Repositories\PlayerFootness\PlayerFootnessInterface;
use App\Http\Requests\Dashboard\StorePlayerFootnessRequest;
use App\Http\Requests\Dashboard\UpdatePlayerFootnessRequest;
use App\Http\Resources\Dashboard\PlayerFootnessResource;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlayerFootnessController extends Controller
{

    public function __construct(private PlayerFootnessInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function findById($id)
    {
        $model = cacheGet('player_footnesses_' . $id);
        if (!$model) {
            $model = $this->modelInterface->findById($id);
            if (!$model) {
                return responseJson(404, __('messages.data not found'));
            } else {
                cachePut('player_footnesses_' . $id, $model);
            }
        }

        return responseJson(200, 'success', new PlayerFootnessResource($model));
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('player_footnesses');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('player_footnesses', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', PlayerFootnessResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StorePlayerFootnessRequest $request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(UpdatePlayerFootnessRequest $request, $id)
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
