<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class UserChatListResource extends JsonResource
{
  public function toArray($request)
  {
    $file = $this->files && $this->files[0] ? $this->files[0] : null;

    return [
      'username' => $this->username,
      'email' => $this->email,
      'phone' => $this->phone,
      'city' => $this->city?->locale?->name,
      'user_type' => new UserTypeResource($this->resource_user_type),
      'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
      'is_messagable' => $this->is_messagable,
    ];
  }
}
