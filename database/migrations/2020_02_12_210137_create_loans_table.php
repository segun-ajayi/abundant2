<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('member_id');
            $table->integer('duration')->default('12');
            $table->string('loan_type')->default('normal');
            $table->float('amount');
            $table->float('balance');
            $table->date('lpDate')->default(Carbon::now());
            $table->integer('refer1')->default(0);
            $table->integer('refer2')->default(0);
            $table->integer('granted_by')->default(0);
            $table->string('mode')->default('bank');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('loans');
    }
}
