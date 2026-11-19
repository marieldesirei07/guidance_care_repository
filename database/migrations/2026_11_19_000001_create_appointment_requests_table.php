<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A student's request for an appointment, made before a case or schedule exists.
     * Status: Pending -> Scheduled (counselor booked a real appointment) or Declined.
     * Kept separate from `appointments`, where every row must belong to a case.
     */
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->dateTime('preferred_datetime');
            $table->text('purpose')->nullable();
            $table->string('status', 20)->default('Pending'); // Pending, Scheduled, Declined
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->string('counselor_remarks', 500)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
