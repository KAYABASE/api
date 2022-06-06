<?php

namespace App\Http\Requests\Column;

use App\Models\Column;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ColumnUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', Column::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'length' => ['nullable', 'integer'],
            'default' => ['nullable', 'string'],
            'nullable' => ['nullable', 'boolean'],
            'unique' => ['nullable', 'boolean'],
            'auto_increment' => ['nullable', 'boolean'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
