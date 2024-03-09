<?php

namespace App\Http\Resources\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         return [
            'id' => $this->id,
            'title' => $this->title,
            'email' => $this->email,
            'phone' =>  $this->phone,
            'message'=> $this->message,
            'created_at'=> $this->created_at,
            'read_at'=> $this->read_at,
            'read-since' => $this->created_at ? Carbon::parse($this->read_at)->diffForHumans(null, true, true) : null,
            'created-since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];
    }
}
