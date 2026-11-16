<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sample_id_code')->unique();
            $table->string('accession_no')->unique();
            $table->string('barcode_value')->unique();
            $table->string('sample_type');
            $table->string('container_type');
            $table->decimal('quantity_ml', 6, 2)->default(3.0);
            $table->dateTime('collected_at')->nullable();
            $table->string('collection_location')->default('Main Lab Phlebotomy');
            $table->foreignId('collector_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('received_at')->nullable();
            $table->string('storage_location')->default('Rack A-01');
            $table->string('status')->default('Registered'); // Registered, Collected, In-Transit, Received, Processing, Completed, Rejected, Disposed
            $table->string('rejection_reason')->nullable();
            $table->boolean('is_stat')->default(false);
            $table->foreignId('transferred_to_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('disposal_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('home_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('phlebotomist_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('booking_code')->unique();
            $table->string('address');
            $table->string('route_zone')->default('Zone A - Central');
            $table->date('scheduled_date');
            $table->string('time_slot');
            $table->decimal('collection_charge', 8, 2)->default(150);
            $table->decimal('cash_to_collect', 10, 2)->default(0);
            $table->decimal('cash_collected', 10, 2)->default(0);
            $table->json('checklist')->nullable();
            $table->string('status')->default('Pending'); // Pending, Assigned, En-Route, Collected, Delivered to Lab, Cancelled
            $table->dateTime('picked_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sample_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('priority')->default('Routine'); // Routine, Urgent, STAT
            $table->string('status')->default('Pending'); // Pending, Sample Received, In-Progress, Rerun, Result Entered, Verified, Approved, Delivered
            $table->boolean('is_repeat')->default(false);
            $table->string('rerun_reason')->nullable();
            $table->string('dilution_factor')->nullable();
            $table->dateTime('tat_due_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('pathologist_signature')->nullable();
            $table->timestamps();
        });

        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_parameter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('parameter_name');
            $table->string('result_value')->nullable();
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->string('flag')->default('Normal'); // Normal, High, Low, Critical High, Critical Low, Abnormal
            $table->string('previous_value')->nullable();
            $table->string('entered_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('critical_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('parameter_name');
            $table->string('result_value');
            $table->string('threshold_info');
            $table->string('alert_level')->default('Critical'); // Critical, Emergency
            $table->boolean('notified_pathologist')->default(true);
            $table->boolean('notified_technician')->default(true);
            $table->boolean('notified_doctor')->default(false);
            $table->string('status')->default('Open'); // Open, Acknowledged, Escalated
            $table->string('acknowledged_by')->nullable();
            $table->dateTime('acknowledged_at')->nullable();
            $table->text('communication_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('report_no')->unique();
            $table->string('verification_code')->unique();
            $table->string('language')->default('en'); // en, hi
            $table->string('status')->default('Approved'); // Draft, Approved, Delivered, Revised
            $table->integer('revision_no')->default(1);
            $table->string('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->json('delivered_via')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_reports');
        Schema::dropIfExists('critical_alerts');
        Schema::dropIfExists('test_results');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('home_collections');
        Schema::dropIfExists('samples');
    }
};

