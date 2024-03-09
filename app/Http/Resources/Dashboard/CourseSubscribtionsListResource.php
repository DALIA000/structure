<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class CourseSubscribtionsListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user' => new UsersShortListResource($this->user),
        ];
    }
}
