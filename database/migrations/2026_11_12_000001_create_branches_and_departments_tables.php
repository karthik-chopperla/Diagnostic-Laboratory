<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('is_central')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('license_number')->nullable();
            $table->string('accreditation_status')->default('NABL Accredited');
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('head_name')->nullable();
            $table->integer('turn_around_hours')->default(6);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('role'); // Pathologist, Radiologist, Technician, Phlebotomist, Receptionist, Admin
            $table->string('qualification')->nullable();
            $table->string('license_no')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('shift')->default('Morning (08:00 - 16:00)');
            $table->string('status')->default('Active');
            $table->date('joined_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('branches');
    }
};

