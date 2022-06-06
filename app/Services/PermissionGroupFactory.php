<?php

namespace App\Services;

use App\Services\PermissionFactory;

class PermissionGroupFactory extends PermissionFactory
{
    public function __construct(protected string $group)
    {
    }

    public function make($name, $exceptAbilities = ['force delete', 'restore'], $extraAbilities = [], ?string $groupName = null): self
    {
        return parent::make($name, $exceptAbilities, $extraAbilities, $this->group);
    }

    protected function withGuardAttribute(array $attributes)
    {
        if (isset($attributes['name'])) {
            $attributes['name'] = $attributes['name'] . ' ' . $this->group;
        }

        return array_merge($attributes, ['guard_name' => $this->guard]);
    }
}
