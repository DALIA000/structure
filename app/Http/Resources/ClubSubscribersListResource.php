<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubSubscribersListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $ends_at = null;
        switch ($this->plan->club_plan_type->slug) {
            case 'monthly':
                $ends_at = Carbon::parse($this->ends_at)->addMonth();
                break;
            case 'annual':
                $ends_at = Carbon::parse($this->ends_at)->addYear();
                break;
            case 'biannual': 
                $ends_at = Carbon::parse($this->ends_at)->addMonths(6);
                break;
        }
        $data = [
            // 'id'=> $this->user_id,
            'user' => new UsersShortListResource($this->user),
            'plan' => new ClubPlanTypesListResource($this->plan->club_plan_type),
            'ends_at' => $ends_at->format('Y/m/d'),
        ];
        return $data;
    }
}
