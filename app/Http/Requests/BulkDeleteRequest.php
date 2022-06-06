<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class BulkDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows("delete");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "ids" => ["required", "array"]
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Silme işlemi için id\'ler gereklidir.',
        ];
    }

    public function payload()
    {
        return $this->only(array_keys($this->rules()));
    }
}
