<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('fee_ledger_account_id')
                ->nullable()
                ->after('fees_set_by')
                ->constrained('ledger_accounts')
                ->nullOnDelete();
        });

        Schema::table('student_payments', function (Blueprint $table) {
            $table->foreignId('ledger_account_id')
                ->nullable()
                ->after('counselor_id')
                ->constrained('ledger_accounts')
                ->nullOnDelete();
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->foreignId('student_payment_id')
                ->nullable()
                ->after('lead_payment_id')
                ->constrained('student_payments')
                ->nullOnDelete();
            $table->unique('student_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropUnique(['student_payment_id']);
            $table->dropConstrainedForeignId('student_payment_id');
        });

        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ledger_account_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fee_ledger_account_id');
        });
    }
};
