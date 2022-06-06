<?php

namespace App\Http\Resources;

use App\Models\BaseModel as Model;
use Illuminate\Support\Str;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Resource
 *
 * @package App\Http\Resources
 */
abstract class Resource extends JsonResource
{
    protected function appendAvailableRelations($data)
    {
        /** @var Model $model */
        $model = $this->resource;

        $model->getRelationList()->each(function ($relationName) use (&$data) {
            $relation = $this->whenLoaded($relationName);

            if (!is_a($relation, MissingValue::class)) {
                $data[Str::snake($relationName)] = Model::toResource($relation);
            }
        });

        return $data;
    }
}
