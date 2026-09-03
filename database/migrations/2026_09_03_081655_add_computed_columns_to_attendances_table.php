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
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('computed_status')->nullable()->after('is_locked');
            $table->decimal('computed_hours', 5, 2)->default(0)->after('computed_status');
            $table->boolean('is_late')->default(0)->after('computed_hours');
            $table->string('status_reason')->nullable()->after('is_late');
            $table->boolean('is_overridden')->default(0)->after('status_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'computed_status',
                'computed_hours',
                'is_late',
                'status_reason',
                'is_overridden',
            ]);
        });
    }
};
