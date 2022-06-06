<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Value extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'row_id',
        'column_id',
        'value'
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function row()
    {
        return $this->belongsTo(Row::class);
    }

    // public static function all($columns = ['*'])
    // {
    //     return static::query()->get(
    //         is_array($columns) ? $columns : func_get_args()
    //     );
    // }
}