<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCollectionsToPlaylists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('collection_project');
        Schema::dropIfExists('collections');
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('author_id');
            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->string('name')->index();
            $table->integer('access_level');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playlists');
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('author_id');
            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->string('name')->index();
            $table->integer('access_level');
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
