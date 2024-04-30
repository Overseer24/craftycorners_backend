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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->time('startTime')->nullable();
            $table->time('endTime')->nullable();
            $table->datetime('startRecur')->nullable();
            $table->datetime('endRecur')->nullable();
            $table->json('daysofweek')->nullable();
            $table->string('backgroundColor');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
