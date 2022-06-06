<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Database extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
