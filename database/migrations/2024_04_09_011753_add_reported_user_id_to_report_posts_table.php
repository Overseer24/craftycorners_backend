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
        Schema::table('report_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_user_id')->nullable();
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_posts', function (Blueprint $table) {
            $table->dropForeign(['reported_user_id']);
            $table->dropColumn('reported_user_id');
        });
    }
};
