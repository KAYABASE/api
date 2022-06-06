<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends UserStoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', User::find($this->route('user')));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        $id = $this->route('user');

        // Find email unique rule index and replace it with unique rule for current courier
        $emailRuleIndex = array_search('unique:users', $rules['email']);

        $rules['email'][$emailRuleIndex] = Rule::unique('users', 'email')->ignore($id);

        // Find phone unique rule index and replace it with unique rule for current courier
        $phoneRuleIndex = array_search('unique:users', $rules['phone']);

        $rules['phone'][$phoneRuleIndex] = Rule::unique('users', 'phone')->ignore($id);

        return $rules;
    }
}
