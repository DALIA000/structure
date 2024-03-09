<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Document\DocumentInterface;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Services\ResponseService;

class DocumentController extends Controller
{
    public function __construct(private DocumentInterface $DocumentI, private ResponseService $responseService)
    {
    }

    public function documents(Request $request)
    {
        $document = $this->DocumentI->documents($request);
        
        if (!$document) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }
        
        if (!$document['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $document['errors']]);
        }
        
        $data = DocumentResource::collection($document['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function upload(CreateDocumentRequest $request)
    {
        $document = $this->DocumentI->upload($request);
        
        if (!$document) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$document['status']) {
            return $this->responseService->json('Fail!', [], 400, $document['errors']);
        }
        $data = new DocumentResource($document['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
