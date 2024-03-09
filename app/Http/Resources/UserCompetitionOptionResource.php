<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCompetitionOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UsersShortListResource($this->user->account),
            'value' => $this->user_competition_options?->first()?->option?->value,
            'is_right_option' => $this->user_competition_options?->first()?->option?->is_right_option,
        ];
    }
}
