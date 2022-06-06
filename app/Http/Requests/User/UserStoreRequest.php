<?php

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use App\Models\Auth\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Enum\Laravel\Rules\EnumRule;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['nullable', 'phone:AUTO', 'unique:users'],
            'password' => ['required', 'string', Password::min(6)],
            'roles' => ['required', 'array'],
            'roles.*' => [
                'required', 'string', 'distinct',
                Rule::exists('roles', 'name')->whereNot('name', Role::SUPER)
            ],
        ];
    }
}
