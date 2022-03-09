<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginRes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'mobile' => '+98' . $this['mobile'],
            'accessToken' => $this['access_token'],
        ];
    }
}
