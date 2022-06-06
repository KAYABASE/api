<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Row extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'table_id',
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
