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
        Schema::create('wafid_appointments', function (Blueprint $table) {
            $table->id();
             $table->string('passport')->unique();
            $table->string('merchant_reference')->nullable();
            $table->string('gcc_slip_no')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('nationality')->nullable();
            $table->string('national_id')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('country_traveling_to')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('passport_expiry_date')->nullable();
            $table->string('passport_issue_place')->nullable();
            $table->string('passport_issue_date')->nullable();
            $table->string('applied_position')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('amount')->nullable();
            $table->string('appointment_type')->nullable();
            $table->string('medical_center_name')->nullable();
            $table->text('medical_center_address')->nullable();
            $table->string('medical_center_phone')->nullable();
            $table->string('medical_center_email')->nullable();
            $table->string('medical_center_website')->nullable();
            $table->string('barcode')->nullable();
            $table->string('generated_date')->nullable();
            $table->string('valid_till')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wafid_appointments');
    }
};
