<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\Dashboard\PlayerPositionResource;
use App\Http\Requests\Dashboard\StorePlayerPositionRequest;
use App\Http\Requests\Dashboard\UpdatePlayerPositionRequest;
use App\Http\Repositories\PlayerPosition\PlayerPositionInterface;

class PlayerPositionController extends Controller
{

    public function __construct(private PlayerPositionInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function findById($id)
    {
        $model = cacheGet('player_positions_' . $id);
        if (!$model) {
            $model = $this->modelInterface->findById($id);
            if (!$model) {
                return responseJson(404, __('messages.data not found'));
            } else {
                cachePut('player_positions_' . $id, $model);
            }
        }

        return responseJson(200, 'success', new PlayerPositionResource($model));
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('player_positions');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('player_positions', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', PlayerPositionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StorePlayerPositionRequest $request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(UpdatePlayerPositionRequest $request, $id)
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
