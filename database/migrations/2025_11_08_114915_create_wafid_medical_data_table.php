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
        Schema::create('wafid_medical_data', function (Blueprint $table) {
            $table->id();
             $table->string('status')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('print_url')->nullable();
            $table->text('photo')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('passport')->unique();
            $table->string('age')->nullable();
            $table->string('passport_expiry_on')->nullable();
            $table->string('nationality_name')->nullable();
            $table->string('applied_position_name')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('traveled_country_name')->nullable();
            $table->string('height')->nullable();
            $table->string('medical_center')->nullable();
            $table->string('weight')->nullable();
            $table->string('medical_examination_date')->nullable();
            $table->string('BMI')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wafid_medical_data');
    }
};
