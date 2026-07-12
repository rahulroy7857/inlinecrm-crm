<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('counselor_fee', 12, 2)->nullable()->after('paid_at');
            $table->decimal('college_fee', 12, 2)->nullable()->after('counselor_fee');
            $table->date('counselor_fee_due_date')->nullable()->after('college_fee');
            $table->date('college_fee_due_date')->nullable()->after('counselor_fee_due_date');
            $table->timestamp('fees_set_at')->nullable()->after('college_fee_due_date');
            $table->foreignId('fees_set_by')->nullable()->after('fees_set_at')->constrained('counselors')->nullOnDelete();
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        Schema::table('student_payments', function (Blueprint $table) {
            $table->string('purpose')->default('application_fee')->after('student_id');
            $table->foreignId('counselor_id')->nullable()->after('purpose')->constrained('counselors')->nullOnDelete();
            $table->string('remark')->nullable()->after('metadata');
            $table->timestamp('receipt_sent_at')->nullable()->after('paid_at');
            $table->foreignId('recorded_by_admin_id')->nullable()->after('receipt_sent_at')->constrained('admins')->nullOnDelete();
            $table->index(['purpose', 'status']);
        });

        Schema::create('student_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('lead_ref')->nullable();
            $table->string('otp_hash');
            $table->json('payload');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_otps');

        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('counselor_id');
            $table->dropConstrainedForeignId('recorded_by_admin_id');
            $table->dropColumn(['purpose', 'remark', 'receipt_sent_at']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fees_set_by');
            $table->dropColumn([
                'counselor_fee',
                'college_fee',
                'counselor_fee_due_date',
                'college_fee_due_date',
                'fees_set_at',
                'email_verified_at',
            ]);
        });
    }
};
