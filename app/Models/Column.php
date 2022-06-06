<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'value',
        'table_id',
        'type',
        'length',
        'nullable',
        'is_unique',
        'default',
        'auto_increment',
        'comment',
    ];

    protected $casts = [
        'nullable' => 'boolean',
        'auto_increment' => 'boolean',
        'is_unique' => 'boolean',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function values()
    {
        return $this->hasMany(Value::class);
    }
}
