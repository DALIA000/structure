<?php

namespace App\Http\Resources\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class CoursesListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->video?->user?->files && $this->video?->user?->files[0] ? $this->video?->user?->files[0] : null;

        return [
            'id' => $this->video_id,
            'cover' => $this->video?->cover ? url($this->video?->cover) : File::find(2)?->media?->first()?->original_url,
            'title' => $this->title,
            'individual_price' => $this->individual_price,
            'group_discount' => $this->group_discount,
            'subscribers_count' => $this->subscribtions_count ?? 0,
            'is_active' => $this->stage,
            'seats' => $this->seats_count,
            'available_seats' => $this->available_seats,
            'sessions_count' => $this->sessions_count,
            'status' => new StatusListResource($this->status),
            'user' => [
                'username' => $this->video?->user?->username,
                'first_name' => $this->video?->user?->user?->first_name,
                'last_name' => $this->video?->user?->user?->last_name,
                'media' => $file ? new FileResource($file) : null,
            ],
            'created_at'=> $this->created_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];
    }
}
