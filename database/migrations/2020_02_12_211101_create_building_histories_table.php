<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('building_id');
            $table->date('date');
            $table->float('credit', 10, 2)->default(0);
            $table->float('debit', 10, 2)->default(0);
            $table->string('mode');
            $table->integer('entered_by');
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
        Schema::dropIfExists('building_histories');
    }
}
