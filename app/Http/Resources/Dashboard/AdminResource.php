<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Role;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $role = $this->roles->first();

        return
            [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => [
                'id' => $role?->id,
                'name' => $role?->name,
            ],
            'permissions' => $this->permissionsArr,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
