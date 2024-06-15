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
        Schema::create('lottos', function (Blueprint $table) {
            $table->id();
            $table->integer('total_amount')->default(0);
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('comission', 8, 2)->default(0);
            $table->decimal('commission_amount', 8, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
             $table->string('slip_no')->default('ttt-3d-123456');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottos');
    }
};
