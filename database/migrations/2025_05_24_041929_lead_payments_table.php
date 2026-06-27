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
        Schema::create('lead_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->date('payment_date');
            $table->enum('payment_type', ['Application Fee', 'Reservation Fee', 'Admission Fee', 'Commission', 'Tuition Fee', 'Refund',  'Other']);
            $table->enum('payment_mode', ['Cash', 'Card', 'UPI', 'Bank Transfer', 'Cheque', 'RazorPay', 'Other']);
            $table->enum('transaction_type', [
                '1', // Received From Student
                '2', // Received From Agent
                '3', // Received From College
                '4', // Paid To Student
                '5', // Paid To Agent
                '6', // Paid To College
                '7', // Other
            ]);
            $table->decimal('amount', 10, 2);
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_payments');
    }
};
