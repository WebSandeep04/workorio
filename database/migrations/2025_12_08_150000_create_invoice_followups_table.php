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
        
        Schema::create('invoice_followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount_paid', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('next_followup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_followups');
    }
};

