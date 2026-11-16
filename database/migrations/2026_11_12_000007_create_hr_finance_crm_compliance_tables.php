<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('shift');
            $table->string('status')->default('Present'); // Present, Absent, Leave, Half-Day
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Income, Expense
            $table->string('category'); // Patient Billing, Corporate Settlement, Reagent Purchase, Salary, Vendor Payment, Maintenance
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('Bank Transfer');
            $table->string('reference_no')->nullable();
            $table->date('transaction_date');
            $table->string('recorded_by')->default('Accounts');
            $table->timestamps();
        });

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('source')->default('Website'); // Website, WhatsApp, Walk-in, Health Camp, Doctor Referral
            $table->string('campaign_name')->nullable();
            $table->string('interest')->nullable();
            $table->string('status')->default('New'); // New, Contacted, Booked, Converted, Lost
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket_no')->unique();
            $table->string('subject');
            $table->string('category')->default('Report'); // Report, Billing, Home Collection, Feedback, Complaint
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->string('status')->default('Open'); // Open, In Progress, Resolved
            $table->integer('rating')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('action');
            $table->string('module');
            $table->string('record_id')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('compliance_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('doc_type')->default('NABL'); // NABL, Licence, Accreditation, SOP, Consent Policy, Retention Policy
            $table->string('reference_no');
            $table->date('valid_until')->nullable();
            $table->string('status')->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // Analyzer, Imaging, Messaging, Payment, Printing, Accounting
            $table->string('protocol'); // HL7 v2.5, ASTM E1394, DICOM/PACS, FHIR R4, REST API, ESC/POS
            $table->string('endpoint_or_port');
            $table->string('status')->default('Connected'); // Connected, Sandbox, Disconnected
            $table->dateTime('last_sync_at')->nullable();
            $table->text('config_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_configs');
        Schema::dropIfExists('compliance_docs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('employee_attendances');
    }
};

