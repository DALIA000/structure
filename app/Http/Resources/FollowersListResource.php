<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowersListResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $file = $user->files && $user->files[0] ? $user->files[0] : null;

        $data = [
            'username' => $user->username,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
        ];

        if ($user->business_name !== null) {
            $data['business_name'] = $user->business_name;
        }else{
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;
        }

        if ($request->self) {
            $data['is_pending'] = $this->is_pending;
        }

        return $data;
    }
}
