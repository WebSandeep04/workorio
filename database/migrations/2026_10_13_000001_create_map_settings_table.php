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
        // Category 1: Tenant-Only Migration (Skip on Master DB)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('map_settings')) {
            Schema::create('map_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_byo_enabled')->default(false);
                $table->string('preferred_provider')->default('google'); // 'google' or 'ola'
                $table->text('google_api_key')->nullable();
                $table->unsignedBigInteger('google_limit')->default(10000);
                $table->unsignedBigInteger('google_usage')->default(0);
                $table->text('ola_api_key')->nullable();
                $table->unsignedBigInteger('ola_limit')->default(50000);
                $table->unsignedBigInteger('ola_usage')->default(0);
                $table->date('billing_cycle_start')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('map_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('map_settings', 'google_limit')) {
                    $table->unsignedBigInteger('google_limit')->default(10000)->after('google_api_key');
                }
                if (!Schema::hasColumn('map_settings', 'google_usage')) {
                    $table->unsignedBigInteger('google_usage')->default(0)->after('google_limit');
                }
                if (!Schema::hasColumn('map_settings', 'ola_limit')) {
                    $table->unsignedBigInteger('ola_limit')->default(50000)->after('ola_api_key');
                }
                if (!Schema::hasColumn('map_settings', 'ola_usage')) {
                    $table->unsignedBigInteger('ola_usage')->default(0)->after('ola_limit');
                }
                if (!Schema::hasColumn('map_settings', 'billing_cycle_start')) {
                    $table->date('billing_cycle_start')->nullable()->after('ola_usage');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('map_settings');
    }
};
