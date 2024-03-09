<?php

namespace App\Http\Resources;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanSubscribtionsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $file = $this->plan->club && $this->plan->club->files && $this->plan->club->files[0] ? $this->plan->club->files[0] : null;
        $endedAt = null;

        if ($this->created_at !== null) {
            switch ($this->plan->club_plan_type->slug) {
                case 'monthly':
                    $endedAt = Carbon::parse($this->created_at)->addMonths(1);
                    break;
                case 'annual':
                    $endedAt = Carbon::parse($this->created_at)->addYears(1);
                    break;
                case 'biannual':
                    $endedAt = Carbon::parse($this->created_at)->addYears(2);
                    break;
            }
        }

        return [
            'number' => $this->club_member?->number,
            'status' => $this->status,
            'club' => $this->plan->club->business_name,
            'type' => $this->plan->club_plan_type->slug,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'created_at' => $this->created_at->format('Y-m-d'),
            'ended_at' => $endedAt ? $endedAt->format('Y-m-d') : null,
        ];
    }
}
