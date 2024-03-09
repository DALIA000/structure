<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitioneSubscribtionsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
{
    $endedAt = null;
    if ($this->competition->created_at !== null) {
        $endedAt = Carbon::parse($this->created_at)->addDays($this->competition->days);
    }

    return [
        'title' => $this->competition->title ?? null,
        'created_at' => $this->competition->created_at ? $this->competition->created_at->format('Y-m-d') : null,
        'ended_at' => $endedAt ? $endedAt->format('Y-m-d') : null,
    ];
}}
