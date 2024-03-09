<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Repositories\Permission\PermissionInterface;
use App\Http\Resources\Dashboard\PermissionResource;
use App\Http\Resources\Dashboard\PermissionsListResource;
use App\Services\ResponseService;

class PermissionController extends Controller
{
    private $PermissionI;

    public function __construct(PermissionInterface $PermissionI, public ResponseService $responseService)
    {
        $this->PermissionI = $PermissionI;
    }

    public function permissions(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
          $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
          $request->merge(['sort' => 'created_at']);
        }

        $permissions = $this->PermissionI->models($request);

        if (!$permissions) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$permissions['status']) {
            return $this->responseService->json('Fail!', [], 400, $permissions['errors']);
        }

        $permissions = $permissions['data'];
        $data = PermissionsListResource::collection($permissions)?->groupBy('model')->map(fn ($i) => ['name' => $i[0]->locale?->group, 'permissions' => PermissionResource::collection($i)]);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function permission(Request $request)
    {
        $permission = $this->PermissionI->findById($request->id);
        if (!$permission) {
            return $this->jsonResponse('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->PermissionI->model)])]]);
        }
        $data = new PermissionResource($permission);
        return $this->jsonResponse('Success!', $data, 200);
    }
}
