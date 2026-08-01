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
        $employees = DB::table('employees')->whereNotNull('shift_id')->get();
        $now = now();
        $inserts = [];
        
        foreach ($employees as $emp) {
            $inserts[] = [
                'employee_id' => $emp->id,
                'shift_id' => $emp->shift_id,
                'effective_from' => '2000-01-01', // Extremely old date to serve as baseline
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($inserts)) {
            // Batch insert in chunks to avoid max placeholder limits
            foreach (array_chunk($inserts, 500) as $chunk) {
                DB::table('employee_shifts')->insert($chunk);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('employee_shifts')->where('effective_from', '2000-01-01')->delete();
    }
};
