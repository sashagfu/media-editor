<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCirclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circles', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->index()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('description');
            $table->unsignedInteger('creator_id');
            $table->string('type');
            $table->unsignedInteger('post_adding_privacy')->default(1);
            $table->unsignedInteger('members_block_privacy')->default(1);
            $table->timestamps();

            $table->foreign('creator_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('circles');
    }
}
