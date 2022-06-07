<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMethodParameterToRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('method', ['GET', 'POST', 'PUT', 'DESTROY'])->default('GET');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('requests', 'method')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('method');
            });
        }
    }
}
