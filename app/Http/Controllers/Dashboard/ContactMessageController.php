<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ContactMessage\ContactMessageInterface;
use App\Http\Resources\Dashboard\ContactMessageResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function __construct(private ContactMessageInterface $ContactI, private ResponseService $responseService)
    {
        $this->ContactI = $ContactI;
    }

     public function messages(Request $request)
    {
        $messages = $this->ContactI->models($request);

        if (!$messages) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messagess.error')]]);
        }

        if (!$messages['status']) {
            return $this->responseService->json('Fail!', [], 400, $messages->errors);
        }

        $data = ContactMessageResource::collection($messages['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function read(Request $request, $id)
    {
        $model = $this->ContactI->read($id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unread(Request $request, $id)
    {
        $model = $this->ContactI->unread($id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $model = $this->ContactI->delete($id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
