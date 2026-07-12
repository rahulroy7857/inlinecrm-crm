<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_account_id')->constrained('ledger_accounts')->cascadeOnDelete();
            $table->foreignId('to_ledger_account_id')->nullable()->constrained('ledger_accounts')->nullOnDelete();
            $table->foreignId('lead_payment_id')->nullable()->constrained('lead_payments')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('accounts')->nullOnDelete();
            $table->date('transaction_date');
            $table->enum('entry_type', ['credit', 'debit']);
            $table->enum('category', ['income', 'expense', 'transfer', 'other'])->default('other');
            $table->string('reference_no')->nullable();
            $table->string('party_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_crm_synced')->default(false);
            $table->timestamps();

            $table->unique('lead_payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
