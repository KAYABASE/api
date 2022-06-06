<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRowIdToValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('values', function (Blueprint $table) {
            $table->foreignId('row_id')->nullable()->constrained('rows')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('values', 'row_id')) {
            Schema::table('values', function (Blueprint $table) {
                $table->dropForeign(['row_id']);
                $table->dropColumn('row_id');
            });
        }
    }
}
