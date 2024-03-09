<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{
    User,
    ContactMessage,
    Video
};
use App\Services\ResponseService;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function __construct(private User $user, private ContactMessage $contactMessage, private Video $video, public ResponseService $responseService)
    {
    }

    public function overview(Request $request)
    {
        $users_count = $this->user->count();
        $videos_count = $this->video->count();
        $contact_messages_count = $this->contactMessage->where('read_at', null)->count();

        $overview = [
            'users_count' => $users_count,
            'videos_count' => $videos_count,
            'contact_messages_count' => $contact_messages_count
        ];

        return $this->responseService->json('Success!', $overview, 200);
    }
}
