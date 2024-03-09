<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionsListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->user->files && $this->user->files[0] ? $this->user->files[0] : null;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'ends_at_days' => $this->ends_at?->d ?: 0,
            'ends_at_hours' => $this->ends_at?->h ?: 0,
            'winners_count' => $this->winners_count,
            'media' => $this->files ? new FileResource($this->files[0]) : null,
            'has_subscribed' => (boolean)$this->has_subscribed->count(), 
            'has_participated' => (boolean)$this->has_subscribed->count(), 
            'price' => $this->price,
            'created_at' => $this->created_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
            'winners' => UsersShortListResource::collection($this->winners),
            'style' => [
                'id' => $this->style,
                'slug' => $this->style === 1 ? 'description' : 'options'
            ],
            'is_active' => $this->status,
            // 'likes_count' => $this->likes_count ?? 0,
            // 'comments_count' => $this->comments_count ?? 0,
            // 'views_count' => $this->views_count ?? 0,
            // 'shares_count' => $this->shares_count ?? 0,
            // 'has_liked' => (boolean)$this->has_liked->count(),
            // 'type' => 1, // video
        ];
    }
}
