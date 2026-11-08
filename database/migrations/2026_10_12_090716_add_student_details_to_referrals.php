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
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('student_name')->nullable()->after('student_id');
            $table->string('student_number')->nullable()->after('student_name');
            $table->string('year_section')->nullable()->after('student_number');
            $table->foreignId('student_id')->nullable()->change(); // allow null if not linked
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['student_name', 'student_number', 'year_section']);
        });
    }
};
