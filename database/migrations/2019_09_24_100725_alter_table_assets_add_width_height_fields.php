<?php

use App\Models\Asset;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAssetsAddWidthHeightFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->integer('height')->after('frames')->nullable();
            $table->integer('width')->after('height')->nullable();
        });

        Asset::query()->update([
            'width' => '1280',
            'height' => '720',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->dropColumn('width');
        });
    }
}
