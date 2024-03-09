<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMinifiedResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'username' => $this->username,
      'email' => $this->email,
      'phone' => $this->phone,
      'city' => $this->city?->locale?->name,
      'user_type' => new UserTypeResource($this->resource_user_type),
    ];
  }
}
