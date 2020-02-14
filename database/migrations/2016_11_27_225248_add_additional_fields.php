<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('name', 255)->default('');
            $table->string('sprite_path', 255)->default('');
            $table->double('time')->unsigned()->default(0.00);
            $table->unsignedInteger('frames')->default(0);
            $table->unsignedInteger('height')->default(0);
            $table->unsignedInteger('width')->default(0);
            $table->string('waveform')->default('');
            $table->unsignedInteger('videoable_id')->nullable();
            $table->string('videoable_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'sprite_path',
                'time',
                'frames',
            ]);
        });
    }
}
