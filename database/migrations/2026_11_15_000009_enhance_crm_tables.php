<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->decimal('expected_value', 10, 2)->default(1500)->after('interest');
            $table->string('assigned_to')->default('CRM Desk')->after('expected_value');
            $table->dateTime('last_contacted_at')->nullable()->after('follow_up_date');
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->text('resolution_notes')->nullable()->after('description');
            $table->string('resolved_by')->nullable()->after('resolution_notes');
            $table->dateTime('resolved_at')->nullable()->after('resolved_by');
        });

        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('target_audience'); // Senior Citizens, Corporates, Diabetics, General Wellness
            $table->string('promo_code')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(15);
            $table->integer('target_leads')->default(100);
            $table->decimal('budget', 12, 2)->default(25000);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('Active'); // Active, Scheduled, Completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('preventive_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('reminder_title');
            $table->string('test_name');
            $table->integer('frequency_months')->default(3);
            $table->date('due_date');
            $table->string('channel')->default('WhatsApp'); // WhatsApp, SMS, Call
            $table->string('status')->default('Due'); // Due, Sent, Booked, Dismissed
            $table->dateTime('last_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_reminders');
        Schema::dropIfExists('crm_campaigns');
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['resolution_notes', 'resolved_by', 'resolved_at']);
        });
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropColumn(['expected_value', 'assigned_to', 'last_contacted_at']);
        });
    }
};

