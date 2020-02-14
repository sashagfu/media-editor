<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProjectInputsRenameFileIdIntoObjectId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_inputs', function (Blueprint $table) {
            $table->renameColumn('file_id', 'object_id');
        });
        Schema::table('project_inputs', function (Blueprint $table) {
            $table->renameColumn('file_type', 'type');
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
            $table->renameColumn('object_id', 'file_id');
        });
        Schema::table('project_inputs', function (Blueprint $table) {
            $table->renameColumn('type', 'file_type');
        });
    }
}
