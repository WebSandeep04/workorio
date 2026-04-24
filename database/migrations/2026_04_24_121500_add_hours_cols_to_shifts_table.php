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
        Schema::table('shifts', function (Blueprint $table) {
            $table->decimal('full_day_hr', 8, 2)->nullable()->after('end_time');
            $table->decimal('half_day_hr', 8, 2)->nullable()->after('full_day_hr');
            $table->decimal('extended_hr', 8, 2)->nullable()->after('half_day_hr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['full_day_hr', 'half_day_hr', 'extended_hr']);
        });
    }
};
