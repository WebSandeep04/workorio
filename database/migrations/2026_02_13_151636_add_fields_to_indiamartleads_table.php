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
                if (!Schema::hasColumn('indiamartleads', 'no_of_employees')) {
                    $table->integer('no_of_employees')->default(0)->after('status');
                }
                if (!Schema::hasColumn('indiamartleads', 'remarks')) {
                    $table->longText('remarks')->nullable()->after('no_of_employees');
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
                if (Schema::hasColumn('indiamartleads', 'no_of_employees')) {
                    $table->dropColumn('no_of_employees');
                }
                if (Schema::hasColumn('indiamartleads', 'remarks')) {
                    $table->dropColumn('remarks');
                }
            });
        }
    }
};
