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
        });

        DB::table('levels')->insert([
           ['level' => 1, 'experience_required' => 0],
           ['level' => 2, 'experience_required' => 1000],
           ['level' => 3, 'experience_required' => 1500],
           ['level' => 4, 'experience_required' => 2500],
           ['level' => 5, 'experience_required' => 4000],
           ['level' => 6, 'experience_required' => 5000],
           ['level' => 7, 'experience_required' => 6500],
           ['level' => 8, 'experience_required' => 10000],
           ['level' => 9, 'experience_required' => 15000],
           ['level' => 10, 'experience_required' => 20000],

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
