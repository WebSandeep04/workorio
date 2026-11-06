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

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'anniversary_date')) {
                $table->date('anniversary_date')->nullable()->after('date_of_birth');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'anniversary_date')) {
                $table->dropColumn('anniversary_date');
            }
        });
    }
};

