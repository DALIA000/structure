<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use App\Http\Repositories\Role\RoleInterface;
use App\Http\Resources\Dashboard\RoleResource;
use App\Http\Resources\Dashboard\RolesListResource;
use App\Http\Requests\Dashboard\CreateRoleRequest;
use App\Http\Requests\Dashboard\EditRoleRequest;
use App\Traits\ResponseTrait;

class RoleController extends Controller
{
    public function __construct(private RoleInterface $RoleI, private ResponseService $responseService)
    {
    }

    public function roles(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        $request->merge(['with' => [
            'locale',
        ]]);

        $roles = $this->RoleI->models($request);
        
        if(!$roles){
            return $this->responseService->json('fail', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$roles['status']){
            return $this->responseService->json('Fail!', [], 400, $roles['errors']);
        }

        $data = RolesListResource::collection($roles['data']);
        return $this->responseService->json('success', $data, 200);
    }

    public function role(Request $request, $id)
    {
        $role = $this->RoleI->findById($id);
        if (!$role) {
            return $this->responseService->json('fail', [], 400, ['error' => [trans('crud.notfound')]]);
        }
        $data = new RoleResource($role);
        return $this->responseService->json('success', $data, 200);
    }

    public function create(CreateRoleRequest $request)
    {
        $role = $this->RoleI->create($request);
        if (!$role) {
            return $this->responseService->json('fail', [], 400, ['error' => [trans('message.error')]]);
        }
        $data = new RoleResource($role);
        return $this->responseService->json('success', $data, 200);
    }

    public function edit(EditRoleRequest $request, $id)
    {
        $role = $this->RoleI->edit($request, $id);
        if (!$role) {
            return $this->responseService->json('fail', [], 400, ['error' => [trans('message.error')]]);
        }
        if (!$role['status']) {
            return $this->responseService->json('fail', [], 400, $role['errors']);
        }

        $data = new RoleResource($role['data']);
        return $this->responseService->json('success', $data, 200);
    }

    public function delete(Request $request, $id)
    {
        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->RoleI->forceDelete($id);
        } else {
            $deleted = $this->RoleI->delete($id);
        }

        if (!$deleted) {
            return $this->responseService->json('fail', [], 400, ['error' => [trans('message.error')]]);
        }

        return $this->responseService->json('success', [], 200);
    }
}
