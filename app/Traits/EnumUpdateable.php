<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait EnumUpdateable
{
    /**
     * Updates the given enum in the database.
     * 
     * @return boolean
     * @throws \Throwable
     */
    public function enumUpdate($table, $column, $enum_types, $nullable = true, $default = null)
    {
        if ($default && !in_array($default, $enum_types, true)) {
            $default = null;
        }

        Schema::table($table, function (Blueprint $_table) use ($column) {
            $_table->renameColumn($column, 'tmpName');
        });

        Schema::table($table, function (Blueprint $_table) use ($column, $enum_types, $nullable, $default) {
            if ($nullable) {
                $_table->enum($column, $enum_types)->nullable();
            } else if ($default) {
                $_table->enum($column, $enum_types)->default($default);
            } else {
                $_table->enum($column, $enum_types);
            }
        });

        $all = DB::table($table)->get();
        foreach ($all as $item) {
            DB::table($table)->where('id', $item->id)->update([$column => $item->tmpName]);
        }

        Schema::table($table, function (Blueprint $_table) {
            $_table->dropColumn('tmpName');
        });

        return true;
    }
}
