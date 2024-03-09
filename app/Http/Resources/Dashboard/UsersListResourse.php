<?php

namespace App\Http\Resources\Dashboard;
use App\Models\Academy;
use App\Models\Business;
use App\Models\Club;
use App\Models\Fan;
use App\Models\Federation;
use App\Models\File;
use App\Models\Influencer;
use App\Models\Journalist;
use App\Models\Player;
use App\Models\Trainer;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersListResourse extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        $data = [
            'user_id' => $this->id,
            'id' => $user?->id,
            'username' => $this->username,
            'email' => $this->email,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'followings_count' => $this->follows_count,
            'followers_count' => $this->followers_count,
            'videos_count' => (int) $this->videos_count,
            'account' => $this->account_is_public,
            'created_at' => $this->created_at,
            'type' => $this->user_type->slug,
            'status' => new StatusListResource($user?->status),
            'business_name' => $user?->business_name,
            'first_name' => $user?->first_name,
            'last_name' => $user?->last_name,
        ];

        return $data;
    }
}
