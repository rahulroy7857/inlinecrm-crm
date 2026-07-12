<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('registration_fee_plan')->nullable()->after('fee_ledger_account_id');
            $table->decimal('registration_fee', 12, 2)->nullable()->after('registration_fee_plan');
            $table->foreignId('fees_set_by_account_id')
                ->nullable()
                ->after('fees_set_by')
                ->constrained('accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fees_set_by_account_id');
            $table->dropColumn(['registration_fee_plan', 'registration_fee']);
        });
    }
};
