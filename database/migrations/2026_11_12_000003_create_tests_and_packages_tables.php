<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('test_categories')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sample_type')->default('Whole Blood');
            $table->string('container_type')->default('EDTA (Lavender)');
            $table->string('sample_quantity')->default('3 mL');
            $table->integer('tat_hours')->default(4);
            $table->string('methodology')->default('Automated Analyzer');
            $table->string('unit')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('preparation_instructions')->nullable();
            $table->boolean('fasting_required')->default(false);
            $table->string('critical_low')->nullable();
            $table->string('critical_high')->nullable();
            $table->string('ref_range_male')->nullable();
            $table->string('ref_range_female')->nullable();
            $table->string('ref_range_child')->nullable();
            $table->json('branch_availability')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('test_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('unit')->nullable();
            $table->string('data_type')->default('numeric'); // numeric, text
            $table->string('ref_min_male')->nullable();
            $table->string('ref_max_male')->nullable();
            $table->string('ref_min_female')->nullable();
            $table->string('ref_max_female')->nullable();
            $table->string('ref_min_child')->nullable();
            $table->string('ref_max_child')->nullable();
            $table->string('critical_low')->nullable();
            $table->string('critical_high')->nullable();
            $table->string('formula')->nullable();
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });

        Schema::create('test_packages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('Preventive');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(15);
            $table->decimal('final_price', 10, 2);
            $table->boolean('is_corporate')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('package_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_test');
        Schema::dropIfExists('test_packages');
        Schema::dropIfExists('test_parameters');
        Schema::dropIfExists('tests');
        Schema::dropIfExists('test_categories');
    }
};

