<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuccessfulTransactionRes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'type' => $this['inquiry_type'],
            'refCode' => $this['inquiry_ref_code'],
            'date' => $this['inquiry_date'],
            'time' => $this['inquiry_time'],
            'sequence' => $this['inquiry_sequence']
        ];
    }
}
