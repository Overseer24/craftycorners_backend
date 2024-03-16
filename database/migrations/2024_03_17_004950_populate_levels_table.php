<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LevelUp\Experience\Models\Level;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Level::add(
            ['level' => 1, 'next_level_experience' => null],
            ['level' => 2, 'next_level_experience' => 1000],
            ['level' => 3, 'next_level_experience' => 1500],
            ['level' => 4, 'next_level_experience' => 2500],
            ['level' => 5, 'next_level_experience' => 4000]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
