<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LikesUsersListResource extends JsonResource
{
    public function toArray($request)
    {
        return new UsersShortListResource($this->user);
    }
}
