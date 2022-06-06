<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    use LogsActivity, HasFactory;

    /**
     * The users with this role can do anything
     *
     * @var string
     */
    const SUPER = 'super admin';

    /**
     * The users with this role can do access the admin panel
     *
     * @var string
     */
    const ADMIN = 'admin';

    /**
     * The users with this role can do access the courier application.
     *
     * @var string
     */
    const COURIER = 'courier';

    /**
     * The users with this role can do just customer related actions.
     *
     * @var string
     */
    const CUSTOMER = 'customer';

    const DEFAULTS = [
        self::SUPER => 'api',
        self::ADMIN => 'api',
        self::COURIER => 'api',
        self::CUSTOMER => 'api'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function scopeWithoutDefaults($builder)
    {
        return $builder->whereNotIn('name', array_keys(self::DEFAULTS));
    }

    public function scopeOnlyDefaults($builder)
    {
        return $builder->whereIn('name', array_keys(self::DEFAULTS));
    }

    public static function prepareDefaults()
    {
        foreach (self::DEFAULTS as $role => $guard) {
            self::firstOrCreate([
                'name' => $role,
                'guard_name' => $guard
            ]);
        }
    }
}
