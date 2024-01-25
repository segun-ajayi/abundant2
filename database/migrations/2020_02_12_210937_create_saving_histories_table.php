<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saving_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('saving_id');
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
        Schema::dropIfExists('saving_histories');
    }
}
