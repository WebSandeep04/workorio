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
        Schema::table('customers', function (Blueprint $table) {
        $table->unsignedBigInteger('prospectus_id')->nullable()->after('id');
        $table->foreign('prospectus_id')->references('id')->on('prospectuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
        $table->dropForeign(['prospectus_id']);
        $table->dropColumn('prospectus_id');
        });
    }
};

