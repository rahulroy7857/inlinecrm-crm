<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselor_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained('counselors')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('ledger_account_id')->nullable()->constrained('ledger_accounts')->nullOnDelete();
            $table->string('payment_mode', 50)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['counselor_id', 'year', 'month'], 'counselor_salary_month_unique');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->foreignId('counselor_salary_payment_id')
                ->nullable()
                ->after('student_payment_id')
                ->constrained('counselor_salary_payments')
                ->nullOnDelete();
            $table->unique('counselor_salary_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropUnique(['counselor_salary_payment_id']);
            $table->dropConstrainedForeignId('counselor_salary_payment_id');
        });

        Schema::dropIfExists('counselor_salary_payments');
    }
};
