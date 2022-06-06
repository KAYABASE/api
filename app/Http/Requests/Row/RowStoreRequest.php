<?php

namespace App\Http\Requests\Row;

use App\Models\Row;
use App\Models\Table;
use App\Models\Value;
use App\Rules\CheckValueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class RowStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', [Row::class, Value::class]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $table = Table::findOrFail($this->route('table'));
        return [
            'values' => ['required', 'array'],
            'values.*.column_id' => ['required', 'integer', 'exists:columns,id,deleted_at,NULL'],
            'values.*.value' => ["required_unless:values.*.value,$table->auto_increment", 'string', 'max:255', new CheckValueRule()],
        ];
    }
}
