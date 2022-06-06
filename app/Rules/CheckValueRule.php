<?php

namespace App\Rules;

use App\Models\Column;
use Illuminate\Contracts\Validation\Rule;

class CheckValueRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(public Column|null $column = null, public $id = null)
    {
        $this->message = "ERROR: The value is not valid.";
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
        if (!$this->column) {
            $arr = explode('.', $attribute);
            $id = request()->get($arr[0])[$arr[1]]['column_id'];
            $this->column = Column::findOrFail($id);
        }

        return $this->checkUnique($value) &&
            $this->checkLength($value) &&
            $this->checkNullable($value) &&
            $this->checkType($value) &&
            $this->checkA_i($value);
    }

    public function checkUnique($value)
    {
        $this->message = "ERROR (SQL): The value ($value) is already exists.";

        if (!$this->column->is_unique) {
            return true;
        }

        if (!$this->id && $this->column->values()->where('value', $value)->first()) {
            return false;
        } else if ($this->column->values()->where('value', $value)->where('id', '!=', $this->id)->count() > 0) {
            return false;
        }

        return true;
    }

    public function checkLength($value)
    {
        $this->message = "ERROR (SQL): The value is too long.";

        if ($this->column->type == 'varchar') {
            return strlen($value) <= $this->column->length;
        }

        return true;
    }

    public function checkNullable($value)
    {
        $this->message = "ERROR (SQL): The value is required.";

        if (!$this->column->nullable) {
            return !empty($value);
        }

        return true;
    }

    public function checkType($value)
    {
        if ($this->column->type == 'varchar') {
            $this->message = "ERROR (SQL): The value type is must be a varchar.";
            return is_string($value);
        } else if ($this->column->type == 'integer') {
            $this->message = "ERROR (SQL): The value type is must be an integer.";
            return is_integer(intVal($value));
        } else if ($this->column->type == 'decimal') {
            $this->message = "ERROR (SQL): The value type is must be a numeric.";
            return is_numeric(floatVal($value));
        } else if ($this->column->type == 'bool') {
            $this->message = "ERROR (SQL): The value type is must be a boolean.";
            return is_bool(boolVal($value));
        }

        return true;
    }

    public function checkA_i($value)
    {
        $this->message = "ERROR (SQL): The value type is must be an integer.";

        if ($this->column->auto_increment) {
            return is_integer(intVal($value));
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
