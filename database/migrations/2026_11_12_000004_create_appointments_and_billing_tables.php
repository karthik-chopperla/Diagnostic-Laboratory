<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('test_id')->nullable()->constrained()->nullOnDelete();
            $table->string('appointment_no')->unique();
            $table->string('appointment_type')->default('Walk-in'); // Walk-in, Online, Home Collection
            $table->dateTime('scheduled_at');
            $table->string('time_slot')->nullable();
            $table->string('status')->default('Scheduled'); // Scheduled, Confirmed, Checked-in, Completed, Cancelled, Rescheduled
            $table->boolean('sms_sent')->default(false);
            $table->boolean('whatsapp_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type')->default('percent'); // percent, fixed
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('corporate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no')->unique();
            $table->string('counter_name')->default('Counter 1');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->string('payment_status')->default('Unpaid'); // Paid, Partial, Unpaid, Refunded
            $table->string('invoice_type')->default('Retail'); // Retail, Corporate
            $table->string('billed_by')->default('Front Desk');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('test_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->string('item_type')->default('Test'); // Test, Package, Home Collection
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_no')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // Cash, UPI, Card, Bank Transfer, Advance
            $table->string('payment_type')->default('Payment'); // Payment, Advance, Refund, Credit Note
            $table->string('transaction_ref')->nullable();
            $table->string('received_by')->default('Cashier');
            $table->dateTime('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('appointments');
    }
};

