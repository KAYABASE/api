<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge([
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
        ], parent::toArray($request), [
            'roles' => $this->whenLoaded('roles',function(){
                return $this->roles->pluck('name');
            }),
            'permissions' => $this->whenLoaded('permissions',function(){
                return $this->permissions->pluck('name');
            })
        ]);
    }
}
