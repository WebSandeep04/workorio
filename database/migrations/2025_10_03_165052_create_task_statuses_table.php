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

        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable(); // For UI styling
            $table->integer('order')->default(0); // For ordering statuses
            $table->timestamps();
        });

        // Insert default task statuses
        DB::table('task_statuses')->insert([
            ['name' => 'Pending', 'color' => '#ffc107', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'In Progress', 'color' => '#17a2b8', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Completed', 'color' => '#28a745', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'On Hold', 'color' => '#6c757d', 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cancelled', 'color' => '#dc3545', 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_statuses');
    }
};

