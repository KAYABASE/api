<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'table_id',
        'query',
        'filter',
        'payload',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
