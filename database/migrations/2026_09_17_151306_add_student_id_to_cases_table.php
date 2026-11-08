<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            if (!Schema::hasColumn('cases', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('cases', 'case_number')) {
                $table->string('case_number')->unique()->after('student_id');
            }
            if (!Schema::hasColumn('cases', 'concern_type')) {
                $table->string('concern_type')->after('case_number');
            }
            if (!Schema::hasColumn('cases', 'status')) {
                $table->string('status')->default('Pending')->after('concern_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn(['case_number', 'concern_type', 'status']);
        });
    }
};