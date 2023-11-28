<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dividends', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->decimal('amount', 8, 2, true);
            $table->decimal('shared', 8, 2, true);
            $table->decimal('excess', 8, 2, true);
            $table->decimal('paid', 8, 2, true);
            $table->decimal('unpaid', 8, 2, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dividends');
    }
};
