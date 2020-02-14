<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCollectionProjectToPlaylistProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('collection_project');
        Schema::create('playlist_project', function (Blueprint $table) {
            $table->unsignedInteger('playlist_id');
            $table->foreign('playlist_id')
                  ->references('id')
                  ->on('playlists')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->unsignedInteger('project_id');
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->unique(['playlist_id', 'project_id']);
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
        Schema::dropIfExists('playlist_project');
        Schema::create('collection_project', function (Blueprint $table) {
            $table->unsignedInteger('collection_id');
            $table->unsignedInteger('project_id');
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->unique(['collection_id', 'project_id']);
            $table->timestamps();
        });
    }
}
