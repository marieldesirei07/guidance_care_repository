<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('referral_id')->nullable()->constrained('referrals')->onDelete('set null');
            $table->foreignId('request_id')->nullable()->constrained('counseling_requests')->onDelete('set null');
            $table->string('concern_type');
            $table->text('summary')->nullable();
            $table->enum('status', [
                'Pending',
                'Ongoing',
                'For Follow-up',
                'Completed',
                'Referred',
                'Closed'
            ])->default('Pending');
            $table->date('opened_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};