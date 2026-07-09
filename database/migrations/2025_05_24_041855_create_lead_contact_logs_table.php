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
        Schema::create('lead_contact_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->dateTime('contact_date');
            $table->text('remark')->nullable();
            $table->integer('duration')->nullable(); // in minutes
            $table->enum('type', ['Call', 'Email', 'SMS', 'WhatsApp', 'In-Person', 'Other']);
            $table->string('status', 30)->default('New'); 
            $table->string('response_type', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_contact_logs');
    }
};
