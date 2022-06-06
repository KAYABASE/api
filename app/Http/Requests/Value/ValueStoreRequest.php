<?php

namespace App\Http\Requests\Value;

use App\Models\Column;
use App\Models\Value;
use App\Rules\CheckValueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ValueStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', Value::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $column = Column::findOrFail($this->column_id);
        return [
            'row_id' => ['required', 'integer', 'exists:rows,id,deleted_at,NULL'],
            'column_id' => ['required', 'integer', 'exists:columns,id,deleted_at,NULL'],
            'value' => ["required_unless:value,$column->auto_increment", 'string', 'max:255', new CheckValueRule($column)],
        ];
    }
}
