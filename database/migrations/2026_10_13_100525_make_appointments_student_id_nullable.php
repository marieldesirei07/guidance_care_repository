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
        // SQLite-friendly: rebuild the table
        Schema::table('appointments', function (Blueprint $table) {
            // Drop foreign key if exists - SQLite often ignores this
        });

        DB::statement('CREATE TABLE appointments_temp AS SELECT * FROM appointments');
        Schema::drop('appointments');

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('appointment_datetime');
            $table->string('location')->nullable();
            $table->text('purpose')->nullable();
            $table->string('status')->default('Scheduled');
            $table->timestamps();
        });

        DB::statement('INSERT INTO appointments (id, case_id, student_id, counselor_id, appointment_datetime, location, purpose, status, created_at, updated_at)
            SELECT id, case_id, student_id, counselor_id, appointment_datetime, location, purpose, status, created_at, updated_at FROM appointments_temp');

        Schema::drop('appointments_temp');
    }

    public function down(): void
    {
        // optional
    }
};
