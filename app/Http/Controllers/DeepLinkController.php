<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ContactMessage\ContactMessageInterface;
use App\Http\Requests\CreateContactMessageRequest;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class DeepLinkController extends Controller
{
    public function __construct(private ContactMessageInterface $ContactI, private ResponseService $responseService)
    {
        $this->ContactI = $ContactI;
    }

    public function video(Request $request, $id)
    {
        $id = $request->get('id');
        $userAgent = $request->server('HTTP_USER_AGENT');

        if (strpos(strtolower($userAgent), 'iphone') !== false) {
            return redirect()->to(
                env('DEEP_LINK_IOS_URL') . '/' . $id
            );
        } elseif (strpos(strtolower($userAgent), 'android') !== false) {
            return redirect()->to(
                env('DEEP_LINK_ANDROID_URL') . '/' . $id
            );
        }

    }
}
