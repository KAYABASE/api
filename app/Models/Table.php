<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'database_id',
    ];

    public function database()
    {
        return $this->belongsTo(Database::class);
    }

    public function columns()
    {
        return $this->hasMany(Column::class);
    }

    public function rows()
    {
        return $this->hasMany(Row::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
