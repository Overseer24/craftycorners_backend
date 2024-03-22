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
        Schema::table('levels', function (Blueprint $table) {
            //add badge to levels with .svg format
            $table->string('badge')->nullable();

            DB::table('levels')->insert([
                ['badge' => 'Beginner.svg'],
                ['badge' => 'Beginner.svg'],
                ['badge' => 'Intermediate.svg'],
                ['badge' => 'Intermediate.svg'],
                ['badge' => 'Advanced.svg'],
                ['badge' => 'Advanced.svg'],
                ['badge' => 'Expert.svg'],
                ['badge' => 'Expert.svg'],
                ['badge' => 'Master.svg'],
                ['badge' => 'Grand Master.svg'],
            ]);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            //
        });
    }
};
