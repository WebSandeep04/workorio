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
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (Schema::hasTable('calendar_events') && !Schema::hasColumn('calendar_events', 'event_date')) {
            Schema::table('calendar_events', function (Blueprint $table) {
                $table->date('event_date')->nullable()->after('name');
                $table->index('event_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('calendar_events') && Schema::hasColumn('calendar_events', 'event_date')) {
            Schema::table('calendar_events', function (Blueprint $table) {
                $table->dropIndex(['event_date']);
                $table->dropColumn('event_date');
            });
        }
    }
};

