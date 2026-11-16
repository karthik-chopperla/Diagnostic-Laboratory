<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyzers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('equipment_code')->unique();
            $table->string('name');
            $table->string('manufacturer');
            $table->string('model');
            $table->string('location');
            $table->string('interface_type')->default('HL7 / Bidirectional');
            $table->string('status')->default('Online'); // Online, Maintenance, Breakdown, Offline
            $table->date('last_calibrated_at')->nullable();
            $table->date('next_calibration_at')->nullable();
            $table->date('last_maintenance_at')->nullable();
            $table->date('next_maintenance_at')->nullable();
            $table->integer('total_tests_run')->default(0);
            $table->decimal('downtime_hours', 6, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('analyzer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analyzer_id')->constrained()->cascadeOnDelete();
            $table->string('log_type'); // Calibration, Preventive Maintenance, Breakdown, Service
            $table->text('description');
            $table->string('performed_by');
            $table->dateTime('logged_at');
            $table->integer('downtime_minutes')->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('reagents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('Reagent'); // Reagent, Consumable, Control
            $table->string('supplier_name');
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->decimal('stock_qty', 10, 2)->default(0);
            $table->decimal('min_stock_qty', 10, 2)->default(10);
            $table->string('unit')->default('Packs');
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->string('status')->default('In Stock'); // In Stock, Low Stock, Expiring Soon, Out of Stock
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('po_number')->unique();
            $table->string('supplier_name');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('Ordered'); // Draft, Ordered, Received, Cancelled
            $table->text('items_summary');
            $table->timestamps();
        });

        Schema::create('qc_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('analyzer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('material_name');
            $table->string('lot_number');
            $table->date('expiry_date');
            $table->string('control_level')->default('Level 1'); // Level 1, Level 2, Level 3
            $table->string('parameter_name');
            $table->decimal('target_mean', 10, 3);
            $table->decimal('sd_value', 10, 3);
            $table->decimal('measured_value', 10, 3);
            $table->decimal('z_score', 6, 2);
            $table->string('westgard_rule')->default('OK'); // OK, 1-2s, 1-3s, 2-2s
            $table->string('status')->default('Accepted'); // Accepted, Warning, Rejected
            $table->string('recorded_by');
            $table->string('approved_by')->nullable();
            $table->dateTime('recorded_at');
            $table->timestamps();
        });

        Schema::create('capa_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('incident_code')->unique();
            $table->string('type')->default('CAPA'); // CAPA, Non-Conformance, Quality Incident
            $table->string('title');
            $table->text('description');
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();
            $table->string('status')->default('Open'); // Open, In Progress, Closed
            $table->string('reported_by');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capa_incidents');
        Schema::dropIfExists('qc_records');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('reagents');
        Schema::dropIfExists('analyzer_logs');
        Schema::dropIfExists('analyzers');
    }
};

