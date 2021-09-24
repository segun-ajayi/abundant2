<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('member_id')->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('sex')->nullable();
            $table->string('marital')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('email')->nullable();
            $table->string('profession')->nullable();
            $table->text('purpose')->nullable();
            $table->integer('referrer')->nullable();
            $table->string('nok')->nullable();
            $table->string('nok_address')->nullable();
            $table->string('nok_phone')->nullable();
            $table->string('nok_phone2')->nullable();
            $table->string('pix')->nullable();
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
        Schema::dropIfExists('members');
    }
}
