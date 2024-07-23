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
        Schema::create('threed_match_times', function (Blueprint $table) {
            
            $table->id();
            $table->date('result_date');
            $table->time('result_time');
            $table->string('match_time'); // Match time description
            $table->enum('status', ['open', 'closed'])->default('closed');
            $table->timestamps();
            // $table->id();
            // $table->string('open_time')->default('00');
            // $table->date('match_time')->default('2023-11-14');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threed_match_times');
    }
};
