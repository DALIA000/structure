<?php

namespace App\Http\Resources\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'prize' => $this->prize,
            'price' => $this->price,
            'days' => $this->days,
            'ends_at_days' => $this->ends_at?->d ?: 0,
            'ends_at_hours' => $this->ends_at?->h ?: 0,
            'winners_count' => $this->winners_count,
            'subscribers_count' => $this->subscribers->count() ,
            'winners' => UsersShortListResource::collection($this->winners),
            'media' => $file ? new FileResource($file) : null,
            'type' => $this->type,
            'created_at'=> $this->created_at->format('y/w/d'),
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];
    }
}
