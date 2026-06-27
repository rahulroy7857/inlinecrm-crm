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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lead_id')->unique();
            $table->enum('status', [
                'New',
                'Hot',
                'Warm',
                'Cold',
                'Fake',
                'Junk',
                'Application',
                'Reservation',
                'Admission',
                'Cancelled'
            ])->default('New');
            $table->dateTime('next_follow_up')->nullable();
            $table->foreignId('source_id')->nullable()->constrained();
            $table->foreignId('counselor_id')->nullable()->constrained('counselors'); // Added this line
            $table->foreignId('academic_year_id')->nullable()->constrained();
            $table->foreignId('course_id')->nullable()->constrained();
            $table->string('specialization')->nullable();
            $table->foreignId('college_id')->nullable()->constrained();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->boolean('transfer_seen')->nullable();
            // Parent Details
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('relation')->nullable();
            
            // Other Details
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('aadhar')->nullable();
            $table->text('notes')->nullable();
            $table->json('languages')->nullable();
            
            // Contact Details
            $table->string('mobile')->nullable();
            $table->string('alternative_mobile')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('father_email')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('guardian_email')->nullable();
            
            // Present Address
            $table->text('present_address')->nullable();
            $table->string('present_country')->nullable();
            $table->string('present_state')->nullable();
            $table->string('present_city')->nullable();
            $table->string('present_place')->nullable();
            $table->string('present_pin')->nullable();
            
            // Permanent Address
            $table->text('permanent_address')->nullable();
            $table->string('permanent_country')->nullable();
            $table->string('permanent_state')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_place')->nullable();
            $table->string('permanent_pin')->nullable();

            //admission details
            $table->string('admission_no')->nullable();
            $table->date('admission_date')->nullable();
            $table->decimal('commission', 10, 2)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents'); 
            $table->decimal('agent_commission', 10, 2)->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('photo')->nullable();

            //Application Details
            $table->date('application_date')->nullable();
            $table->text('application_note')->nullable();

            // Reservation Details
            $table->date('reservation_date')->nullable();
            $table->text('reservation_note')->nullable();

            // Cancellation Details
            $table->date('cancel_date')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->text('cancel_note')->nullable();
            $table->timestamp('picked_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
