<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class UserShortResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        $data = [
            'user_type' => new UserTypeResource($this->resource_user_type),
            'username' => $this->username,
            'email' => $this->email,
            'bio' => $this->bio,
            'birthday' => $this->birthday,
            'club' => new ClubsListResource($this->club),
        ];

        if ($user?->business_name !== null) {
            $data['business_name'] = $user?->business_name;
        }else{
            $data['first_name'] = $user?->first_name;
            $data['last_name'] = $user?->last_name;
        }

        $data = array_merge($data, [
            'username' => $this->username,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'followings_count' => $this->follows_count,
            'followers_count' => $this->followers_count,
            'videos_count' => $this->videos_count,
            'account' => $this->account_is_public
        ]);

        return $data;
    }
}
