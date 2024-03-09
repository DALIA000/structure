<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class UsersListResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        $data = [
            'username' => $this->username,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'has_followed' => (boolean) $this->user?->has_followed?->count(),
            'has_saved' => (boolean) $this->has_saved->count(),
            'followings_count' => $this->follows_count,
            'followers_count' => $this->followers_count,
            'videos_count' => (int) $this->videos_count,
            'account' => $this->account_is_public
        ];

        if ($user?->business_name !== null) {
            $data['business_name'] = $user?->business_name;
        }else{
            $data['first_name'] = $user?->first_name;
            $data['last_name'] = $user?->last_name;
        }

        return $data;
    }
}
