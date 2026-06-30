<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('lead_ref')->nullable()->index();
            $table->foreignId('counselor_id')->nullable()->constrained('counselors')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile', 20);
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('password');
            $table->string('application_status')->default('registered');
            $table->boolean('profile_completed')->default(false);
            $table->timestamp('profile_completed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('payment_status')->default('pending');
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('relation')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('aadhar', 20)->nullable();
            $table->text('present_address')->nullable();
            $table->string('present_city')->nullable();
            $table->string('present_pin', 10)->nullable();
            $table->text('permanent_address')->nullable();
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
