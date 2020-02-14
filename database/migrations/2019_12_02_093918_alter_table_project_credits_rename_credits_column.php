<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProjectCreditsRenameCreditsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_credits', function (Blueprint $table) {
            $table->renameColumn('credits', 'details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_credits', function (Blueprint $table) {
            $table->renameColumn('details', 'credits');
        });
    }
}
