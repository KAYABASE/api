<?php

namespace App\Http\Requests\Database;

use App\Models\Column;
use App\Models\Database;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DatabaseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', [Database::class, Table::class, Column::class]);
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

            'tables' => ['sometimes', 'array'],
            'tables.*.name' => ['sometimes', 'string', 'max:255'],

            'tables.*.columns' => ['sometimes', 'array'],
            'tables.*.columns.*.name' => ['sometimes', 'string', 'max:255'],
            'tables.*.columns.*.type' => ['sometimes', 'string', 'max:255'],
            'tables.*.columns.*.length' => ['nullable', 'integer'],
            'tables.*.columns.*.default' => ['nullable', 'boolean'],
            'tables.*.columns.*.nullable' => ['nullable', 'boolean'],
            'tables.*.columns.*.unique' => ['nullable', 'boolean'],
            'tables.*.columns.*.auto_increment' => ['nullable', 'boolean'],
            'tables.*.columns.*.comment' => ['nullable', 'string'],
        ];
    }
}
