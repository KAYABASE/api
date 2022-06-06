<?php

namespace App\Models;

use App\Traits\DateUtils;
use App\Traits\ModelUtils;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Model
 *
 * @package App\Models
 */
class BaseModel extends EloquentModel
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    /**
     * @param $key
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value) {
            return $value;
        }

        $key_splitted = explode('.', $key);
        $key_first    = Arr::pull($key_splitted, 0);
        $key_target   = implode('.', $key_splitted);

        $value_first = parent::getAttribute($key_first);

        return Arr::get($value_first, $key_target);
    }

    /**
     * Returns implemented relations in model
     *
     * @return Collection
     */
    public function getRelationList(): Collection
    {
        try {
            return collect((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC))
                ->filter(function (?ReflectionMethod $method) {
                    if (is_null($method) || is_null($method->getReturnType())) {
                        return false;
                    }

                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    $returnClassName = $method->getReturnType()->getName();

                    return is_subclass_of($returnClassName, Relation::class);
                })
                ->map(fn (ReflectionMethod $method) => $method->getName())
                ->values();
        } catch (ReflectionException $e) {
            return collect();
        }
    }

    /**
     * Converts model and collections to api resource
     *
     * @param Model|EloquentModel|LengthAwarePaginator|Collection|null $data
     *
     * @return AnonymousResourceCollection|JsonResource|MissingValue|mixed
     */
    public static function toResource($data)
    {
        if (is_subclass_of($data, Model::class)) {
            $className = 'App\\Http\\Resources\\' . class_basename($data) . 'Resource';

            return new $className($data);
        }

        if (is_subclass_of($data, Paginator::class) || is_subclass_of($data, Collection::class)) {
            /** @var Model $model */
            $model = $data->first();

            if (!$model) {
                return new MissingValue;
            }

            $className = 'App\\Http\\Resources\\' . class_basename($model) . 'Resource';

            return call_user_func([$className, 'collection'], $data);
        }

        return $data;
    }

    public function resource()
    {
        return static::toResource($this);
    }

    protected function _getModel() {
        return $this->_model;
    }
}
