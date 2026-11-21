<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * The audit trail ("diary"): who did what, to which record, and when.
 * Rows are only ever added. A logging failure never breaks the action itself.
 */
class ActivityLog
{
    public static function record(string $action, string $subjectType, $subjectId = null, array $meta = []): void
    {
        try {
            DB::table('activity_logs')->insert([
                'actor_id'     => auth()->id(),
                'action'       => $action,
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId,
                'meta'         => $meta ? json_encode($meta) : null,
                'created_at'   => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('Activity log not written: ' . $e->getMessage(), compact('action', 'subjectType', 'subjectId'));
        }
    }
}
