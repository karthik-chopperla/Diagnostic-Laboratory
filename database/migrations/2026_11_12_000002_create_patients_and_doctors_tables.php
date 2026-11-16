<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->default('Corporate'); // Corporate, Hospital, Nursing Home
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(100000);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(10);
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('specialization');
            $table->string('qualification')->nullable();
            $table->string('hospital_clinic')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('referral_source')->default('Direct');
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->boolean('portal_enabled')->default(true);
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('corporate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('uhid')->unique();
            $table->string('name');
            $table->string('gender'); // Male, Female, Other
            $table->date('dob')->nullable();
            $table->integer('age');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('clinical_history')->nullable();
            $table->string('patient_type')->default('Walk-in'); // Walk-in, Registered, Corporate
            $table->foreignId('family_head_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('family_relation')->nullable();
            $table->boolean('is_merged')->default(false);
            $table->unsignedBigInteger('merged_into_id')->nullable();
            $table->boolean('consent_given')->default(true);
            $table->timestamps();
        });

        Schema::create('patient_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('document_type')->default('Prescription');
            $table->string('file_path');
            $table->string('uploaded_by')->default('Front Desk');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_documents');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('corporates');
    }
};

