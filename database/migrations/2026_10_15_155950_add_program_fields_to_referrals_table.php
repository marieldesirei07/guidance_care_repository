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
            $table->unsignedBigInteger('course_id')->nullable()->after('student_id');
            $table->string('year_level')->nullable()->after('course_id');
            $table->unsignedBigInteger('section_id')->nullable()->after('year_level');
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['course_id', 'year_level', 'section_id']);
        });
    }
};
