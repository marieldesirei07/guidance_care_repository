<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('counseling_requests', 'student_name')) {
                $table->string('student_name')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('counseling_requests', 'student_number')) {
                $table->string('student_number')->nullable()->after('student_name');
            }
            if (!Schema::hasColumn('counseling_requests', 'program')) {
                $table->string('program')->nullable()->after('student_number');
            }
            if (!Schema::hasColumn('counseling_requests', 'year_section')) {
                $table->string('year_section')->nullable()->after('program');
            }
            if (!Schema::hasColumn('counseling_requests', 'description')) {
                $table->text('description')->nullable()->after('concern_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('counseling_requests', function (Blueprint $table) {
            $columns = ['student_name', 'student_number', 'program', 'year_section', 'description'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('counseling_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};