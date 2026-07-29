<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() !== 'mysql') { return; }

        if (Schema::hasTable('tenant_databases')) {
            Schema::table('tenant_databases', function (Blueprint $table) {
                if (!Schema::hasColumn('tenant_databases', 'db_host')) {
                    $table->string('db_host')->nullable()->after('connection_name');
                }
                if (!Schema::hasColumn('tenant_databases', 'db_port')) {
                    $table->string('db_port', 10)->nullable()->after('db_host');
                }
                if (!Schema::hasColumn('tenant_databases', 'db_username')) {
                    $table->string('db_username')->nullable()->after('db_port');
                }
                if (!Schema::hasColumn('tenant_databases', 'db_password')) {
                    $table->string('db_password')->nullable()->after('db_username');
                }
            });
        }
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() !== 'mysql') { return; }

        if (Schema::hasTable('tenant_databases')) {
            if (Schema::hasColumn('tenant_databases', 'db_password') || Schema::hasColumn('tenant_databases', 'db_username') || Schema::hasColumn('tenant_databases', 'db_host') || Schema::hasColumn('tenant_databases', 'db_port')) {
                Schema::table('tenant_databases', function (Blueprint $table) {
                    if (Schema::hasColumn('tenant_databases', 'db_password')) {
                        $table->dropColumn('db_password');
                    }
                    if (Schema::hasColumn('tenant_databases', 'db_username')) {
                        $table->dropColumn('db_username');
                    }
                    if (Schema::hasColumn('tenant_databases', 'db_port')) {
                        $table->dropColumn('db_port');
                    }
                    if (Schema::hasColumn('tenant_databases', 'db_host')) {
                        $table->dropColumn('db_host');
                    }
                });
            }
        }
    }
};



