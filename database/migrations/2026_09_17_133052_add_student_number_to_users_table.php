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
        Schema::table('users', function (Blueprint $table) {
            // Skip columns the earlier role migration already created (fresh installs).
            if (!Schema::hasColumn('users', 'student_number')) {
                $table->string('student_number')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->after('student_number');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('employee_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_number', 'employee_id', 'role']);
        });
    }
};
