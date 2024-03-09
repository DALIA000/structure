<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaveHistoryListResource;
use App\Models\User;
use App\Models\Model;
use Illuminate\Http\Request;
use App\Http\Repositories\Save\SaveInterface;
use App\Services\ResponseService;
use App\Http\Resources\SavesListResource;

class SaveController extends Controller
{
    public $SaveI;
    public $loggedinUser;

    public function __construct(SaveInterface $SaveI, public ResponseService $responseService)
    {
        $this->SaveI = $SaveI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function saves(Request $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'user_id' => $user->id,
            'savable_username' => $request->username
        ]);

        $request->merge(['with' => [
            'savable',
        ]]);

        $saves = $this->SaveI->models($request);

        if (!$saves) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$saves['status']) {
            return $this->responseService->json('Fail!', [], 400, $saves['errors']);
        }

        $saves = $saves['data'];

        return $this->responseService->json('Success!', SavesListResource::collection($saves), 200);
    }

    public function saveHistory(Request $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'user_id' => $user->id,
            'savable_username' => $request->username
        ]);

        $request->merge(['with' => [
            'savable',
        ]]);

        $saves = $this->SaveI->models($request);

        if (!$saves) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$saves['status']) {
            return $this->responseService->json('Fail!', [], 400, $saves['errors']);
        }

        $saves = $saves['data'];

        return $this->responseService->json('Success!', SaveHistoryListResource::collection($saves), 200);
    }
}
