<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'compagnie_id' => $this->compagnie_id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'proffession' => $this->proffession,
            'country' => $this->country,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'address' => $this->address,
            'verification_state' => $this->verification_state,
            'status' => $this->status,
            'city' => $this->city,
            'birth_day' => $this->birth_day,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'accessToken' => $this->accessToken,
            'compagny' => $this->compagny,
            'roles' => $this->roles,
        ];
    }
}
