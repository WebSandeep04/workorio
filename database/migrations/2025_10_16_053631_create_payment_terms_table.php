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


        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Payment term name
            $table->text('description')->nullable(); // Payment term description
            $table->integer('advance_percentage')->default(50); // Advance on project confirmation percentage
            $table->integer('design_dev_percentage')->default(30); // Upon design & development approval percentage
            $table->integer('completion_percentage')->default(20); // Upon completion of development before deployment percentage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default payment terms
        DB::table('payment_terms')->insert([
            [
                'name' => 'Standard Payment Terms',
                'description' => 'Standard payment terms for software development projects',
                'advance_percentage' => 50,
                'design_dev_percentage' => 30,
                'completion_percentage' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};
