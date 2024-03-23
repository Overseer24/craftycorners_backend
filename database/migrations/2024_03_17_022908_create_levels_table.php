<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique();
            $table->integer('experience_required');
            $table->string('badge');
        });

        DB::table('levels')->insert([
           ['level' => 1, 'experience_required' => 0,'badge' => 'Beginner.svg'],
           ['level' => 2, 'experience_required' => 1000,'badge' => 'Beginner.svg'],
           ['level' => 3, 'experience_required' => 1500,'badge' => 'Intermediate.svg'],
           ['level' => 4, 'experience_required' => 2500,'badge' => 'Intermediate.svg'],
           ['level' => 5, 'experience_required' => 4000,'badge' => 'Advanced.svg'],
           ['level' => 6, 'experience_required' => 5000,'badge' => 'Advanced.svg'],
           ['level' => 7, 'experience_required' => 6500,'badge' => 'Expert.svg'],
           ['level' => 8, 'experience_required' => 10000,'badge' => 'Expert.svg'],
           ['level' => 9, 'experience_required' => 15000,'badge' => 'Master.svg'],
           ['level' => 10, 'experience_required' => 20000,'badge' => 'Grand Master.svg'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
