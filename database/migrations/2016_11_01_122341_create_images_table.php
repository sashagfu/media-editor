<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->index()->unique();
            $table->string('file_name');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('file_size');
            $table->string('file_path')->nullable();
            $table->string('thumb_path')->nullable();
            $table->unsignedInteger('album_id')->nullable();
            $table->unsignedInteger('imageable_id')->nullable();
            $table->string('imageable_type')->nullable();
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
        Schema::dropIfExists('images');
    }
}
