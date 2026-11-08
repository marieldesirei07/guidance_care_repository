<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('appointments');
    }

    public function down(): void
    {
        Schema::create('appointments', function ($table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('appointment_datetime');
            $table->string('location')->nullable();
            $table->text('purpose')->nullable();
            $table->string('status')->default('Scheduled');
            $table->timestamps();
        });
    }
};