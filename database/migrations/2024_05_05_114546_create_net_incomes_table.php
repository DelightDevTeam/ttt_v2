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
        Schema::create('net_incomes', function (Blueprint $table) {
            $table->id();
            $table->decimal('owner_balance')->nullable();
            $table->decimal('total_income')->nullable();
            $table->decimal('total_win_withdraw')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('net_incomes');
    }
};
