<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunicationAndAuditSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('messages')->upsert([
            ['id' => 1, 'user_id' => 9, 'appointment_id' => 4, 'channel' => 'email', 'subject' => 'Bevestiging afspraak', 'body' => 'Uw afspraak op 24-11-2026 om 09:30 is bevestigd.', 'status' => 'sent', 'sent_at' => '2026-11-18 08:00:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'user_id' => 9, 'appointment_id' => 4, 'channel' => 'sms', 'subject' => null, 'body' => 'Herinnering: afspraak op 24-11 om 09:30 bij Tandarts.', 'status' => 'queued', 'sent_at' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'user_id' => 7, 'appointment_id' => 7, 'channel' => 'email', 'subject' => 'Bevestiging afspraak', 'body' => 'Uw afspraak op 02-12-2026 om 10:00 is bevestigd.', 'status' => 'sent', 'sent_at' => '2026-11-18 08:05:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'user_id' => 10, 'appointment_id' => 5, 'channel' => 'email', 'subject' => 'Aanvraag ontvangen', 'body' => 'Wij hebben uw afspraakaanvraag ontvangen.', 'status' => 'sent', 'sent_at' => '2026-11-17 16:20:00', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['user_id', 'appointment_id', 'channel', 'subject', 'body', 'status', 'sent_at', 'updated_at']);

        DB::table('announcements')->upsert([
            ['id' => 1, 'employee_id' => 1, 'type' => 'dental_tip', 'title' => 'Poets 2x per dag', 'content' => 'Poets twee keer per dag twee minuten en gebruik ragers of floss.', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'employee_id' => 3, 'type' => 'dental_tip', 'title' => 'Minder tussendoortjes', 'content' => 'Vermijd veel suikerhoudende tussendoortjes om gaatjes te voorkomen.', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'employee_id' => 1, 'type' => 'new_treatment', 'title' => 'Tanden bleken nu mogelijk', 'content' => 'Vanaf deze maand bieden wij professionele bleekbehandelingen aan.', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['employee_id', 'type', 'title', 'content', 'published_at', 'updated_at']);

        DB::table('feedback')->upsert([
            ['id' => 1, 'appointment_id' => 1, 'patient_id' => 1, 'rating' => 5, 'comment' => 'Snelle en vriendelijke service.', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'appointment_id' => 2, 'patient_id' => 2, 'rating' => 4, 'comment' => 'Prettige behandeling, wel even moeten wachten.', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'appointment_id' => 3, 'patient_id' => 3, 'rating' => 5, 'comment' => 'Heel zorgvuldig uitgelegd wat er gebeurde.', 'created_at' => $now, 'updated_at' => $now],
        ], ['appointment_id'], ['patient_id', 'rating', 'comment', 'updated_at']);

        DB::table('audit_logs')->upsert([
            ['id' => 1, 'user_id' => 2, 'action' => 'inzien', 'table_name' => 'medical_records', 'record_id' => 1, 'occurred_at' => $now, 'ip_address' => '192.168.1.10', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'user_id' => 5, 'action' => 'wijzigen', 'table_name' => 'medical_records', 'record_id' => 2, 'occurred_at' => $now, 'ip_address' => '192.168.1.11', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'user_id' => 3, 'action' => 'wijzigen', 'table_name' => 'medical_records', 'record_id' => 3, 'occurred_at' => $now, 'ip_address' => '192.168.1.12', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['user_id', 'action', 'table_name', 'record_id', 'occurred_at', 'ip_address', 'updated_at']);
    }
}
