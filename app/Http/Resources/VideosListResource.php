<?php
namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class VideosListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->user?->files && $this->user?->files[0] ? $this->user?->files[0] : null;

        $data = [
            'id' => $this->id,
            'cover' => $this->cover,
            'video' => $this->video,
            'description' => $this->is_course ? $this->course->title : $this->description,
            'user' => [
                'id' => $this->user?->id,
                'username' => $this->user?->username,
                'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
                'has_followed' => (boolean) $this->user?->has_followed?->count(),
            ],
            'likes_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'views_count' => $this->views_count ?? 0,
            'shares_count' => $this->shares_count ?? 0,
            'has_liked' => (boolean) $this->has_liked->count(),
            'has_saved' => (boolean) $this->has_saved->count(),
            'is_promoted' => (boolean) $this->is_promoted,
            'tagged_users' => UsersShortListResource::collection($this->tagged_users),
            'type' => $this->is_course ? 2 : 1, // 1 video, 2 course
            'created_at' => $this->created_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
            'comments_status' => new StatusResource($this->comments_status),
        ];

        if ($this->course) {
            $data['has_subscribed'] = (boolean) $this->course?->has_subscribed?->count();
        }
        
        return $data;
    }
}
