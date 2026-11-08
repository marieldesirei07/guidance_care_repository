<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->foreignId('referred_by')->constrained('users')->onDelete('cascade'); // Counselor
            $table->enum('office', ['Clinic', 'Disciplinary', 'Other']);
            $table->text('reason');
            $table->text('notes_for_office')->nullable(); // Limited information only
            $table->enum('status', ['Pending', 'Received', 'In Progress', 'Completed'])->default('Pending');
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_referrals');
    }
};