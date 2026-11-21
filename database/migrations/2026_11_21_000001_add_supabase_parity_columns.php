<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SQLite build only: adds the columns and table that the Postgres baseline
 * (database/schema/guidancecare_baseline.sql) already has, so the same code runs on both.
 * The Postgres build never runs this folder (it uses --path=database/migrations_pgsql).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        if (! Schema::hasColumn('office_referrals', 'handled_by')) {
            Schema::table('office_referrals', function (Blueprint $table) {
                $table->unsignedBigInteger('handled_by')->nullable();
                $table->timestamp('completed_at')->nullable();
            });
        }

        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('actor_id')->nullable()->index();
                $table->string('action', 100);
                $table->string('subject_type', 50);
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->text('meta')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['subject_type', 'subject_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');

        if (Schema::hasColumn('office_referrals', 'handled_by')) {
            Schema::table('office_referrals', function (Blueprint $table) {
                $table->dropColumn(['handled_by', 'completed_at']);
            });
        }

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
