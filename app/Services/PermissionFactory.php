<?php

namespace App\Services;

use App\Models\Auth\Permission;
use Illuminate\Support\Arr;

class PermissionFactory
{
    public $guard = 'api';

    protected $defaultPermissions = ['create', 'update', 'delete', 'force delete', 'view', 'restore'];

    protected $equivalentPermissions = [
        'c' => 'create',
        'r' => 'view',
        'u' => 'update',
        'd' => 'delete',
        'fd' => 'force delete',
        're' => 'restore'
    ];

    protected $groups = [];

    protected $permissions = [];

    public function make($name, $exceptAbilities = ['force delete', 'restore'], $extraAbilities = [], ?string $groupName = null): self
    {
        $abilities = Arr::except(array_combine($this->defaultPermissions, $this->defaultPermissions), array_values($exceptAbilities));

        $abilities = array_merge($abilities, $extraAbilities);

        foreach ($abilities as $ability) {
            $ability = $this->equivalentPermissions[$ability] ?? $ability;

            $attributes = $this->withGuardAttribute(['name' => $ability . ' ' . $name]);

            $attributes = array_merge(['group' => $groupName ?: $name], $attributes);

            $this->permissions[] = $attributes;
        }

        return $this;
    }

    public function makeWithGroup(string $group, callable $abilities): self
    {
        $this->groups[$group] = $abilities;

        return $this;
    }

    public function only($name, $only = []): self
    {
        return $this->make($name, $this->defaultPermissions, $only);
    }

    protected function withGuardAttribute(array $attributes)
    {
        return array_merge($attributes, ['guard_name' => $this->guard]);
    }

    public function guard(string $name): self
    {
        $this->guard = $name;

        return $this;
    }

    public function build()
    {
        foreach ($this->permissions as $attributes) {
            Permission::firstOrCreate($attributes);
        }

        foreach ($this->groups as $group => $abilities) {
            $group = call_user_func($abilities, resolve(PermissionGroupFactory::class, compact('group')));

            $group->build();
        }
    }
}
