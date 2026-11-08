<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('counseling_requests', 'preferred_datetime')) {
                $table->dateTime('preferred_datetime')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('counseling_requests', function (Blueprint $table) {
            if (Schema::hasColumn('counseling_requests', 'preferred_datetime')) {
                $table->dropColumn('preferred_datetime');
            }
        });
    }
};