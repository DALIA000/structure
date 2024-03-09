<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->video?->user?->files && $this->video?->user?->files[0] ? $this->video?->user?->files[0] : null;

        $data =  [
            'id' => $this->video->id,
            'cover' => $this->video?->cover ? url($this->video?->cover) : File::find(2)?->media?->first()?->original_url,
            'video' => url($this->video?->video),
            'title' => $this->title,
            'description' => $this->description,
            'individual_price' => $this->individual_price,
            'group_discount' => $this->group_discount,
            'user' => [
                'username' => $this->video?->user?->username,
                'first_name' => $this->video?->user?->user?->first_name,
                'last_name' => $this->video?->user?->user?->last_name,
                'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            ],
            'likes_count' => $this->video?->likes_count ?? 0,
            'comments_count' => $this->video?->comments_count ?? 0,
            'views_count' => $this->video?->views_count ?? 0,
            'shares_count' => $this->video?->shares_count ?? 0,
            'subscribers_count' => $this->subscribtions_count ?? 0,
            'is_active' => $this->stage,
            'available_seats' => $this->available_seats,
            'sessions_count' => $this->sessions_count,
            'type' => 2, // course
            'sessions' => SessionsListResource::collection($this->sessions),
            'created_at' => $this->created_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];

        if($this->video?->user?->username == app('loggedinUser')?->username){
            $data['status'] = new StatusListResource($this->status);
            $data['invoice'] = new InvoicesListResource($this->invoice);
            $data['can_start_live'] = $this->can_start_live;
            $data['reject_reason'] = $this->status_note;
        }
        return $data;
    }
}
