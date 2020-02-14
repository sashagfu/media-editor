<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_inputs', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->index()->unique();

            // Product relation
            $table->unsignedInteger('project_id');
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // File relation
            $table->unsignedInteger('file_id')->index();
            $table->string('file_type', 50)->index();

            $table->unsignedTinyInteger('layer_id')->default(0);
            $table->float('position')->default(0.0);
            $table->float('start_from');
            $table->float('length');
            $table->text('transform');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_inputs');
    }
}
