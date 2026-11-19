<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['email', 'sms']);
            $table->string('subject', 150)->nullable();
            $table->text('body');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['upcoming_appointment', 'new_treatment', 'dental_tip']);
            $table->string('title', 150);
            $table->text('content');
            $table->dateTime('published_at')->useCurrent();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE TABLE feedback (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                appointment_id INTEGER NOT NULL UNIQUE REFERENCES appointments(id) ON DELETE CASCADE,
                patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
                rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
                comment TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )');
        } else {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appointment_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->timestamps();
            });

            DB::statement('ALTER TABLE feedback ADD CONSTRAINT chk_feedback_rating CHECK (rating BETWEEN 1 AND 5)');
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('table_name', 50);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->dateTime('occurred_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('messages');
    }
};
