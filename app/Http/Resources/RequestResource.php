<?php

namespace App\Http\Resources;

use App\Http\Requests\Row\RowStoreRequest;
use App\Http\Requests\Row\RowUpdateRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'query' => $this->query,
            'filter' => json_decode($this->filter, true),
            'url' => env("APP_URL") . "/api/requests/{$this->query}",
            'method' => $this->method,
            'payload' => $this->method == 'POST' ? RowStoreRequest::payload() : ($this->method == 'PUT' ? RowUpdateRequest::payload() : null),
        ];
    }
}
