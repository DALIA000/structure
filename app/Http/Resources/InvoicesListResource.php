<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoicesListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cost' => $this->cost,
            'profit_margin' => $this->profit_margin,
            'currency' => $this->currency_id,
            'invoicable' => [
                'type' => $this->invoicable::model()->slug,
                'id' => $this->invoicable?->id,
                'title' => $this->invoicable?->title,
            ],
            'status' => new StatusListResource($this->status),
        ];
    }
}
