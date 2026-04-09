<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('callings', function (Blueprint $table) {
            $table->unsignedBigInteger('list_id')->nullable()->after('id');
            // Adding timestamps as they were missing and useful for audit
            if (!Schema::hasColumn('callings', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('callings', function (Blueprint $table) {
            $table->dropColumn('list_id');
        });
    }
};
