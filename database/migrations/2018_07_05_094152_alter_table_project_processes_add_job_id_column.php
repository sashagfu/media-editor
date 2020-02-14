<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProjectProcessesAddJobIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_processes', function (Blueprint $table) {
            $table->string('job_id')->after('project_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_processes', function (Blueprint $table) {
            $table->dropIndex(['job_id']);
            $table->dropColumn('job_id');
        });
    }
}
