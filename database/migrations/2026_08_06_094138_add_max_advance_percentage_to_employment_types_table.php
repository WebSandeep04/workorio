<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employment_types', function (Blueprint $table) {
            $table->decimal('max_advance_percentage', 5, 2)->default(0)->after('max_loan_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('employment_types', function (Blueprint $table) {
            $table->dropColumn('max_advance_percentage');
        });
    }
};
