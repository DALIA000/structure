<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Block\BlockInterface;
use App\Services\ResponseService;
use App\Http\Resources\BlocksListResource;

class BlockController extends Controller
{
    public $BlockI;
    public $loggedinUser;

    public function __construct(BlockInterface $BlockI, public ResponseService $responseService)
    {
        $this->BlockI = $BlockI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function blocks(Request $request)
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
            'blockable_username' => $request->username,
        ]);

        $request->merge(['with' => [
            'blockable',
        ]]);

        $blocks = $this->BlockI->models($request);

        if (!$blocks) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$blocks['status']) {
            return $this->responseService->json('Fail!', [], 400, $blocks['errors']);
        }

        $blocks = $blocks['data'];

        return $this->responseService->json('Success!', BlocksListResource::collection($blocks), 200);
    }
}
