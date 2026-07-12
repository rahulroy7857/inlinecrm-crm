<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->foreignId('lead_payment_id')
                ->nullable()
                ->after('ledger_account_id')
                ->constrained('lead_payments')
                ->nullOnDelete();
            $table->unique('lead_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropUnique(['lead_payment_id']);
            $table->dropConstrainedForeignId('lead_payment_id');
        });
    }
};
