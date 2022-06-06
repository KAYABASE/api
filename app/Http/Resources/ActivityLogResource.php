<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Relations\Relation;

class ActivityLogResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(
            parent::toArray($request),
            [
                'causer_type' => Relation::getMorphedModel($this->causer_type),
                'subject_type' => Relation::getMorphedModel($this->subject_type),
            ]
        );
    }
}
