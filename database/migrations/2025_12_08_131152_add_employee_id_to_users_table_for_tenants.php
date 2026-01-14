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
        
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id');
                // Only add foreign key if employees table exists
                if (Schema::hasTable('employees')) {
                    $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id')) {
                // Drop foreign key if it exists
                try {
                    $foreignKeys = Schema::getConnection()
                        ->getDoctrineSchemaManager()
                        ->listTableForeignKeys('users');
                    
                    foreach ($foreignKeys as $foreignKey) {
                        if ($foreignKey->getName() === 'users_employee_id_foreign') {
                            $table->dropForeign(['employee_id']);
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // If foreign key doesn't exist, just continue
                }
                
                $table->dropColumn('employee_id');
            }
        });
    }
};
