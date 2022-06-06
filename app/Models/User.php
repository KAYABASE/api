<?php

namespace App\Models;

use App\Models\Auth\Authenticatable;
use App\Models\Auth\Role;
use App\Models\Order\Order;
use App\Models\Cart\Cart;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelFavorite\Favorite;
use Overtrue\LaravelFavorite\Traits\Favoriter;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use HasApiTokens, HasFactory, HasRoles, Favoriter, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'locale'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'name',
    ];

    // Relations
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    protected function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->locale;
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isSuper($guard = null)
    {
        return $this->hasRole(Role::SUPER, $guard);
    }

    public function viewPanel()
    {
        return $this->hasAnyRole(Role::ADMIN);
    }

    public function scopeCourier(Builder $builder)
    {
        return $builder->whereRelation('roles', 'name', Role::COURIER);
    }

    public function scopeWithoutSuperAdmins(Builder $builder)
    {
        return $builder->whereDoesntHave('roles', function (Builder $query) {
            return $query->whereName(Role::SUPER);
        });
    }
}
