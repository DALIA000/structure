<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class CoursesListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->video?->user?->files && $this->video?->user?->files[0] ? $this->video?->user?->files[0] : null;

        $data = [
            'id' => $this->video_id,
            'cover' => $this->video?->cover ? url($this->video?->cover) : File::find(2)?->media?->first()?->original_url,
            'title' => $this->title,
            'is_active' => $this->stage,
            'available_seats' => $this->available_seats,
            'sessions_count' => $this->sessions_count,
            'user' => [
                'username' => $this->video?->user?->username,
                'first_name' => $this->video?->user?->user?->first_name,
                'last_name' => $this->video?->user?->user?->last_name,
                'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            ],
            'created_at' => $this->created_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];

        if($this->video?->user?->username == app('loggedinUser')?->username){
            $data['status'] = new StatusListResource($this->status);
            $data['invoice'] = new InvoicesListResource($this->invoice);
        }

        return $data;
    }
}
