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

class DeleteAccountRequestsListResourse extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        $data = [
            'id' => $this->id,
            'status' => $this->status_id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'user_type' => $this->user_type_model?->slug,
            'followings_count' => $this->followings_count,
            'followers_count' => $this->followers_count,
        ];

        return $data;
    }
}
