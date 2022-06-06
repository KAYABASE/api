<?php

namespace App\Http\Requests\Value;

use App\Models\Column;
use App\Models\Value;
use App\Rules\CheckValueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ValueUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', Value::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $_value = Value::findOrFail($this->route('value'));
        return [
            'value' => ['required', 'string', 'max:255', new CheckValueRule(Column::findOrFail($_value->column_id), $_value->id)],
        ];
    }
}
