<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConvertedFileColumnToProjectInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_inputs', function (Blueprint $table) {
            $table->string('converted_file', 1000)
                ->after('file_type')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_inputs', function (Blueprint $table) {
            $table->dropColumn('converted_file');
        });
    }
}
