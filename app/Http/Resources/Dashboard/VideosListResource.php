<?php
namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class VideosListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = null;
        if ($this->user && $this->user->files && $this->user->files[0]) {
            $file = $this->user->files[0];
        }

        return [
            'id' => $this->id,
            'cover' => $this->cover,
            'user' => [
                'id' => $this->user?->id,
                'username' => $this->user?->username,
                'media' => $file ? new FileResource($file) : null,
            ],
            'likes_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'views_count' => $this->views_count ?? 0,
            'shares_count' => $this->shares_count ?? 0,
            'tagged_users' => UsersShortListResource::collection($this->tagged_users),
            'is_promoted' => (boolean) $this->is_promoted,
            'type' => 1, // video
            'deleted_at' => $this->deleted_at,
        ];
    }
}
