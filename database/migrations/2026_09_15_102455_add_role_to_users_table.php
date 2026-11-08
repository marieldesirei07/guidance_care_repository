<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'student',
                'teacher',
                'counselor',
                'clinic',
                'disciplinary',
                'admin'
            ])->default('student')->after('email');
            
            $table->string('student_number')->nullable()->after('role');
            $table->string('employee_id')->nullable()->after('student_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'student_number', 'employee_id']);
        });
    }
};