<?php

namespace App\Rules;

use App\Models\Table;
use Illuminate\Contracts\Validation\Rule;

class RequestFilterRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(public Table $table)
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $result = true;
        $this->ids = collect([]);

        foreach ($value['ids'] as $key) {
            $column = $this->table->columns->where('id', $key)->first();
            if (!$column) {
                $this->ids->push($key);
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This filter is not valid. The following columns are not valid: ' . $this->ids->implode(', ');
    }
}
