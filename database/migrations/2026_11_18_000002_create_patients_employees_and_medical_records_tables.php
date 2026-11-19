<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('birth_date');
            $table->string('street', 100)->nullable();
            $table->string('house_number', 10)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('insurance_company', 80)->nullable();
            $table->string('policy_number', 30)->nullable();
            $table->boolean('avg_consent')->default(false);
            $table->dateTime('avg_consent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('function', ['tandarts', 'mondhygienist', 'assistent']);
            $table->string('big_number', 20)->nullable();
            $table->string('specialization', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('allergies')->nullable();
            $table->text('medication')->nullable();
            $table->text('conditions')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('patients');
    }
};
