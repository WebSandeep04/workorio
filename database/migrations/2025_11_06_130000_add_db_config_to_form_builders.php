<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('form_builders', function (Blueprint $table) {
            $table->string('db_host')->nullable()->after('fields');
            $table->string('db_port')->nullable()->default('3306')->after('db_host');
            $table->string('db_username')->nullable()->after('db_port');
            $table->string('db_password')->nullable()->after('db_username');
            $table->string('db_database')->nullable()->after('db_password');
        });
    }

    public function down(): void
    {
        Schema::table('form_builders', function (Blueprint $table) {
            $table->dropColumn(['db_host', 'db_port', 'db_username', 'db_password', 'db_database']);
        });
    }
};


