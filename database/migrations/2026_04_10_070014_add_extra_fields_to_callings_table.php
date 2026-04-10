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
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('callings', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->string('contact_person')->nullable()->after('company_name');
            $table->string('legal_status')->nullable()->after('email');
            $table->string('gst_number')->nullable()->after('legal_status');
            $table->string('turnover')->nullable()->after('gst_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('callings', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'contact_person', 'legal_status', 'gst_number', 'turnover']);
        });
    }
};
