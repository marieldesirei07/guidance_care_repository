<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * GuidanceCare baseline for PostgreSQL (Supabase).
 *
 * Builds the final schema in one step from database/schema/guidancecare_baseline.sql.
 * This folder is used ONLY for the Postgres build; the SQLite build keeps database/migrations.
 *
 * Run (after `php artisan db:show` confirms the right database):
 *   php artisan migrate --path=database/migrations_pgsql
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            throw new RuntimeException('The GuidanceCare baseline is for PostgreSQL only. Check DB_CONNECTION.');
        }

        DB::unprepared(file_get_contents(database_path('schema/guidancecare_baseline.sql')));

        // Laravel's own bookkeeping table: same lock as the rest.
        DB::statement('ALTER TABLE migrations ENABLE ROW LEVEL SECURITY');
    }

    public function down(): void
    {
        // Children first, then parents, so no foreign key blocks the drop.
        foreach ([
            'activity_logs', 'office_referrals', 'case_notes', 'appointment_requests', 'appointments',
            'cases', 'counseling_requests', 'referrals',
            'failed_jobs', 'job_batches', 'jobs', 'cache_locks', 'cache', 'sessions', 'password_reset_tokens',
            'users',
        ] as $table) {
            DB::statement("DROP TABLE IF EXISTS {$table}");
        }
    }
};
