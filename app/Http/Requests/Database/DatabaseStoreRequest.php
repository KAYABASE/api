<?php

namespace App\Http\Requests\Database;

use App\Models\Database;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DatabaseStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', [Database::class, Table::class, Column::class]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'tables' => ['nullable', 'array'],
            'tables.*.name' => ['required', 'string', 'max:255'],

            'tables.*.columns' => ['nullable', 'array'],
            'tables.*.columns.*.name' => ['required', 'string', 'max:255'],
            'tables.*.columns.*.type' => ['required', 'string', 'max:255'],
            'tables.*.columns.*.length' => ['nullable', 'integer'],
            'tables.*.columns.*.default' => ['nullable', 'boolean'],
            'tables.*.columns.*.nullable' => ['nullable', 'boolean'],
            'tables.*.columns.*.unique' => ['nullable', 'boolean'],
            'tables.*.columns.*.auto_increment' => ['nullable', 'boolean'],
            'tables.*.columns.*.comment' => ['nullable', 'string'],
        ];
    }
}
