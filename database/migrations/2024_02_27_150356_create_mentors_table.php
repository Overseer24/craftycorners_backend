<?php

use App\Models\Mentor;
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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constraint('users')->onDelete('cascade');
            $table->string('student_id');
            $table->string('Program');
            $table->foreignId('community_id')->constraint('communities')->onDelete('cascade');
            $table->string('date_of_Assessment')->nullable();
            $table->string('specialization');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
