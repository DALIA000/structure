<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Media\MediaInterface;
use App\Http\Requests\CreateMediaRequest;
use App\Http\Requests\CreateMediaVideoRequest;
use App\Http\Resources\MediaResource;
use App\Services\ResponseService;

class MediaController extends Controller
{
    public function __construct(private MediaInterface $MediaI, private ResponseService $responseService)
    {
    }

    public function medias(Request $request)
    {
        $document = $this->MediaI->models($request);
        
        if (!$document) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }
        
        if (!$document['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $document['errors']]);
        }
        
        $data = $document['data'];
        return $this->responseService->json('Success!', $data, 200);
    }

    public function upload(CreateMediaRequest $request)
    {
        $document = $this->MediaI->upload($request);
        
        if (!$document) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$document['status']) {
            return $this->responseService->json('Fail!', [], 400, $document['errors']);
        }
        $data = $document['data'];
        return $this->responseService->json('Success!', $data, 200);
    }

    public function uploadVideo(CreateMediaVideoRequest $request)
    {
        $document = $this->MediaI->upload($request);
        
        if (!$document) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$document['status']) {
            return $this->responseService->json('Fail!', [], 400, $document['errors']);
        }
        $data = $document['data'];
        return $this->responseService->json('Success!', $data, 200);
    }
}
