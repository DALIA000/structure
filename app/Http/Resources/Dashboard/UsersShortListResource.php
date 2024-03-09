<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class UsersShortListResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        $data = [
            'username' => $this->username,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'followings_count' => $this->follows_count,
            'followers_count' => $this->followers_count,
            'videos_count' => $this->videos_count,
            'account' => $this->account_is_Public
        ];

        if ($user->business_name !== null) {
            $data['business_name'] = $user->business_name;
        }else{
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;
        }

        return $data;
    }
}
