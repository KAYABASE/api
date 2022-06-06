<?php

namespace App\Models\Auth;

use Spatie\Permission\Models\Permission as Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected $appends = [
        'translated_name'
    ];

    public function getTranslatedNameAttribute()
    {
        if (!isset($this->attributes['name'])) return '';

        $attribute = $this->attributes['name'];

        $resource = Str::of($group = $this->attributes['group'])->title()->trim()->__toString();

        $action = Str::of($attribute)->before($group)->title()->trim()->__toString();

        return __("$action :resource", ['resource' => __($resource)]);
    }
}
