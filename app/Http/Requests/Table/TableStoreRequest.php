<?php

namespace App\Http\Requests\Table;

use App\Models\Column;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TableStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', [Table::class, Column::class]);
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
            'database_id' => ['required', 'integer', 'exists:databases,id,deleted_at,NULL'],

            'columns' => ['required', 'array'],
            'columns.*.name' => ['required', 'string', 'max:255'],
            'columns.*.type' => ['required', 'string', 'max:255'],
            'columns.*.length' => ['nullable', 'integer'],
            'columns.*.default' => ['nullable', 'boolean'],
            'columns.*.nullable' => ['nullable', 'boolean'],
            'columns.*.unique' => ['nullable', 'boolean'],
            'columns.*.auto_increment' => ['nullable', 'boolean'],
            'columns.*.comment' => ['nullable', 'string'],
        ];
    }
}
