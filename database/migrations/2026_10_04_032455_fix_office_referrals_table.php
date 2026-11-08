<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('office_referrals');

        Schema::create('office_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->foreignId('referred_by')->constrained('users')->onDelete('cascade');
            $table->string('office'); // Clinic, Disciplinary, Other
            $table->text('reason');
            $table->text('notes_for_office')->nullable();
            $table->enum('status', ['Pending', 'Received', 'In Progress', 'Completed', 'Closed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_referrals');
    }
};