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
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'is_document_management_enabled')) {
                $table->boolean('is_document_management_enabled')->default(true)->after('is_subscription_enabled');
            }
        });

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'is_user_setup_enabled')) {
                // Place the column after document management if it exists, otherwise append.
                $afterColumn = Schema::hasColumn('tenants', 'is_document_management_enabled')
                    ? 'is_document_management_enabled'
                    : null;

                $column = $table->boolean('is_user_setup_enabled')->default(true);
                if ($afterColumn) {
                    $column->after($afterColumn);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'is_user_setup_enabled')) {
                $table->dropColumn('is_user_setup_enabled');
            }
            if (Schema::hasColumn('tenants', 'is_document_management_enabled')) {
                $table->dropColumn('is_document_management_enabled');
            }
        });
    }
};

