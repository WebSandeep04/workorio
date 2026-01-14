<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        Schema::create('workflow_dependency_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('allows_lag')->default(false);           
            $table->timestamps();
        });

        DB::table('workflow_dependency_types')->insert([
            [
                'code' => 'FS',
                'name' => 'Finish to Start',
                'description' => 'Successor task begins when the predecessor task finishes.',
                'allows_lag' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SS',
                'name' => 'Start to Start',
                'description' => 'Successor task starts when the predecessor task starts.',
                'allows_lag' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FF',
                'name' => 'Finish to Finish',
                'description' => 'Successor task finishes when the predecessor task finishes.',
                'allows_lag' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SF',
                'name' => 'Start to Finish',
                'description' => 'Successor task finishes only after the predecessor task starts.',
                'allows_lag' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FS+LAG',
                'name' => 'Finish to Start (Lag)',
                'description' => 'Finish-to-start dependency that anticipates a configurable lag period before the successor begins.',
                'allows_lag' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SS+LAG',
                'name' => 'Start to Start (Lag)',
                'description' => 'Start-to-start dependency variant supporting a lag window between predecessor and successor starts.',
                'allows_lag' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_dependency_types');
    }
};

