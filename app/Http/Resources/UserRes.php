<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'status' => $this['status'],
            'clientId' => $this['identifier'],
            'mobile' => '+98' . $this['mobile'],
            'cash' => $this['cash'],
            'information' => [
                'verify' => (!empty($this['first_name']) && !empty($this['last_name']) && !empty($this['bank_account_number'])),
                'name' => trim($this['first_name'] . ' ' . $this['last_name']),
                'bankNumber' => ($this['bank_account_number']) ?? '0',
            ]
        ];
    }
}
