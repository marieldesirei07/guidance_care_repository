<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Safety guard. The migrations in this folder are the SQLite history and cannot build the
 * Postgres (Supabase) schema: they stop half-way and leave broken tables behind.
 * On Postgres, stop before any of them runs and say which command to use instead.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            throw new RuntimeException(
                "Stopped: these are the SQLite migrations. On Supabase run:\n"
                . "  php artisan migrate --path=database/migrations_pgsql"
            );
        }
    }

    public function down(): void
    {
        //
    }
};
