<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Report\ReportInterface;
use App\Http\Repositories\Subscribe\SubscribeInterface;
use App\Http\Requests\ReportMessageRequest;
use App\Models\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Chat\ChatInterface;
use App\Http\Resources\{
    ChatsListResource,
    ChatResource,
};
use App\Services\ResponseService;
use Musonza\Chat\Models\Message;


class ChatController extends Controller
{
    public $loggedinUser;

    public function __construct(private ChatInterface $ChatI, public ReportInterface $ReportI, private SubscribeInterface $SubscribeI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function conversations(Request $request)
    {
        $conversations = $this->ChatI->conversations($request);

        if (!$conversations) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$conversations['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $conversations['errors']]);
        }

        $data = ChatsListResource::collection($conversations['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function conversation(Request $request, $username)
    {
        $conversation = $this->ChatI->conversation($request, $username);

        if (!$conversation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!isset($conversation['data'])) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $conversation['errors']]);
        }

        $data = ChatResource::collection($conversation['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function createConversation(Request $request, $username)
    {
        $chat = $this->ChatI->createConversation($request, $username);

        if (!$chat) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!isset($chat['data'])) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $chat['errors']]);
        }

        $data = ChatsListResource::collection($chat['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function report(ReportMessageRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $request->merge([
            'reportable_id' => $id,
            'reportable_type' => Message::class,
        ]);

        $report = $this->ReportI->create($request, $user->id);

        if (!$report) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$report['status']) {
            return $this->responseService->json('Fail!', [], 400, $report['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
