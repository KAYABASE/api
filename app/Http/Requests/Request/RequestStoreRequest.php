<?php

namespace App\Http\Requests\Request;

use App\Models\Request;
use App\Models\Table;
use App\Rules\FilterDuplicateRule;
use App\Rules\RequestFilterRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class RequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', Request::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $table = $this->route('table');
        return [
            'filter' => ['array', 'required', new RequestFilterRule($table)],
        ];
    }
}
