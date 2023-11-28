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
        Schema::create('dividend_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();
            $table->foreignId('dividend_id')
                ->constrained('dividends')
                ->cascadeOnDelete();
            $table->decimal('amount', 8, 2, true);
            $table->boolean('status')->default(false);
            $table->string('mode')->nullable();
            $table->date('pDate')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dividend_reports');
    }
};
