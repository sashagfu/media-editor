<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProjectInputsAddVolumeLevelsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_inputs', function (Blueprint $table) {
            $table->text('volume_levels')->nullable()->after('transform');
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
            $table->dropColumn('volume_levels');
        });
    }
}
