<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ContactMessage\ContactMessageInterface;
use App\Http\Requests\CreateContactMessageRequest;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function __construct(private ContactMessageInterface $ContactI, private ResponseService $responseService)
    {
        $this->ContactI = $ContactI;
    }

    public function create(CreateContactMessageRequest $request)
    {
        $message = $this->ContactI->create($request);

        if (!$message) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$message['status']) {
            return $this->responseService->json('Fail!', [], 400, $message->errors);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
