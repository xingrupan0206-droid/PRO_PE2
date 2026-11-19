<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentAndBillingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('appointments')->upsert([
            ['id' => 1, 'patient_id' => 1, 'employee_id' => 1, 'treatment_id' => 1, 'starts_at' => '2026-11-10 09:00:00', 'ends_at' => '2026-11-10 09:20:00', 'status' => 'completed', 'comment' => 'Halfjaarlijkse controle', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'patient_id' => 2, 'employee_id' => 3, 'treatment_id' => 2, 'starts_at' => '2026-11-11 10:00:00', 'ends_at' => '2026-11-11 10:30:00', 'status' => 'completed', 'comment' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'patient_id' => 3, 'employee_id' => 2, 'treatment_id' => 3, 'starts_at' => '2026-11-12 14:00:00', 'ends_at' => '2026-11-12 14:45:00', 'status' => 'completed', 'comment' => 'Vulling linksonder', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'patient_id' => 4, 'employee_id' => 1, 'treatment_id' => 1, 'starts_at' => '2026-11-24 09:30:00', 'ends_at' => '2026-11-24 09:50:00', 'status' => 'confirmed', 'comment' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'patient_id' => 5, 'employee_id' => 3, 'treatment_id' => 2, 'starts_at' => '2026-11-25 11:00:00', 'ends_at' => '2026-11-25 11:30:00', 'status' => 'requested', 'comment' => 'Graag een ochtendafspraak', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'patient_id' => 1, 'employee_id' => 2, 'treatment_id' => 5, 'starts_at' => '2026-11-30 13:30:00', 'ends_at' => '2026-11-30 13:45:00', 'status' => 'cancelled', 'comment' => 'Geannuleerd door patiënt', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'patient_id' => 2, 'employee_id' => 1, 'treatment_id' => 4, 'starts_at' => '2026-12-02 10:00:00', 'ends_at' => '2026-12-02 11:30:00', 'status' => 'confirmed', 'comment' => 'Wortelkanaalbehandeling', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['patient_id', 'employee_id', 'treatment_id', 'starts_at', 'ends_at', 'status', 'comment', 'updated_at']);

        DB::table('invoices')->upsert([
            ['id' => 1, 'invoice_number' => 'F-2026-0001', 'patient_id' => 1, 'appointment_id' => 1, 'invoice_date' => '2026-11-10', 'due_date' => '2026-12-10', 'total_amount' => 32.50, 'status' => 'paid', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'invoice_number' => 'F-2026-0002', 'patient_id' => 2, 'appointment_id' => 2, 'invoice_date' => '2026-11-11', 'due_date' => '2026-12-11', 'total_amount' => 55.00, 'status' => 'outstanding', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'invoice_number' => 'F-2026-0003', 'patient_id' => 3, 'appointment_id' => 3, 'invoice_date' => '2026-11-12', 'due_date' => '2026-12-12', 'total_amount' => 120.00, 'status' => 'paid', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['invoice_number', 'patient_id', 'appointment_id', 'invoice_date', 'due_date', 'total_amount', 'status', 'updated_at']);

        DB::table('invoice_lines')->upsert([
            ['id' => 1, 'invoice_id' => 1, 'treatment_id' => 1, 'quantity' => 1, 'unit_price' => 32.50, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'invoice_id' => 2, 'treatment_id' => 2, 'quantity' => 1, 'unit_price' => 55.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'invoice_id' => 3, 'treatment_id' => 3, 'quantity' => 1, 'unit_price' => 95.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'invoice_id' => 3, 'treatment_id' => 5, 'quantity' => 1, 'unit_price' => 25.00, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['invoice_id', 'treatment_id', 'quantity', 'unit_price', 'updated_at']);

        DB::table('payments')->upsert([
            ['id' => 1, 'invoice_id' => 1, 'amount' => 32.50, 'method' => 'ideal', 'transaction_reference' => 'TR-100001', 'paid_at' => '2026-11-10 09:30:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'invoice_id' => 3, 'amount' => 120.00, 'method' => 'pin', 'transaction_reference' => 'TR-100002', 'paid_at' => '2026-11-12 14:55:00', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['invoice_id', 'amount', 'method', 'transaction_reference', 'paid_at', 'updated_at']);
    }
}
