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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('student_id');
            $table->string('Program');
            $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
            $table->string('date_of_Assessment')->nullable();
            $table->string('specialization');
            $table->enum('status', ['pending', 'approved', 'retired', 'for assessment','revoked'])->default('pending');
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
