<?php

namespace App\Http\Requests\Table;

use App\Models\Column;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TableUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', [Table::class, Column::class]);
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

            'columns' => ['sometimes', 'array'],
            'columns.*.name' => ['sometimes', 'string', 'max:255'],
            'columns.*.type' => ['sometimes', 'string', 'max:255'],
            'columns.*.length' => ['nullable', 'integer'],
            'columns.*.default' => ['nullable', 'boolean'],
            'columns.*.nullable' => ['nullable', 'boolean'],
            'columns.*.unique' => ['nullable', 'boolean'],
            'columns.*.auto_increment' => ['nullable', 'boolean'],
            'columns.*.comment' => ['nullable', 'string'],
        ];
    }
}
