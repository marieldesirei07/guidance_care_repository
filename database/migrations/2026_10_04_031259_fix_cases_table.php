<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cases');

        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('referral_id')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
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