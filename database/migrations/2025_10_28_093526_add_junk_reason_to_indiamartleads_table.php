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

        if (Schema::hasTable('indiamartleads')) {
            Schema::table('indiamartleads', function (Blueprint $table) {
                if (!Schema::hasColumn('indiamartleads', 'junk_reason')) {
                    $table->text('junk_reason')->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('indiamartleads')) {
            Schema::table('indiamartleads', function (Blueprint $table) {
                if (Schema::hasColumn('indiamartleads', 'junk_reason')) {
                    $table->dropColumn('junk_reason');
                }
            });
        }
    }
};

