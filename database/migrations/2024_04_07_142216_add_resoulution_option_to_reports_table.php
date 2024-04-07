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
            $table->enum('resolution_option', ['ignore','warn', 'suspend'])->default('ignore');
            $table->timestamp('unsuspend_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_posts', function (Blueprint $table) {
            $table->dropColumn('resolution_option');
            $table->dropColumn('unsuspend_date');
        });
    }
};
