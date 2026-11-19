<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TreatmentAndAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('treatments')->upsert([
            ['id' => 1, 'name' => 'Periodieke controle', 'description' => 'Controle van gebit en tandvlees', 'duration_minutes' => 20, 'price' => 32.50, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Gebitsreiniging', 'description' => 'Verwijderen van tandsteen en aanslag', 'duration_minutes' => 30, 'price' => 55.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Vulling', 'description' => 'Behandelen van een gaatje', 'duration_minutes' => 45, 'price' => 95.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Wortelkanaalbehandeling', 'description' => 'Behandeling van een ontstoken zenuw', 'duration_minutes' => 90, 'price' => 320.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Röntgenfoto', 'description' => 'Intra-orale röntgenfoto', 'duration_minutes' => 15, 'price' => 25.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Tanden bleken', 'description' => 'Professionele bleekbehandeling', 'duration_minutes' => 60, 'price' => 250.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Extractie', 'description' => 'Verwijderen van een tand of kies', 'duration_minutes' => 45, 'price' => 110.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'duration_minutes', 'price', 'is_active', 'updated_at']);

        $hours = [
            [1, 1, '08:30', '12:30'], [1, 1, '13:30', '17:00'],
            [1, 2, '08:30', '12:30'], [1, 3, '08:30', '12:30'], [1, 3, '13:30', '17:00'],
            [1, 4, '08:30', '17:00'], [1, 5, '08:30', '12:30'],
            [2, 2, '09:00', '17:00'], [2, 3, '09:00', '17:00'], [2, 4, '09:00', '13:00'],
            [3, 1, '09:00', '17:00'], [3, 3, '09:00', '17:00'], [3, 5, '09:00', '13:00'],
        ];

        $availabilities = [];
        foreach ($hours as $index => $hour) {
            [$employeeId, $day, $startsAt, $endsAt] = $hour;
            $availabilities[] = [
                'id' => $index + 1,
                'employee_id' => $employeeId,
                'day_of_week' => $day,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('availabilities')->upsert($availabilities, ['id'], ['employee_id', 'day_of_week', 'starts_at', 'ends_at', 'updated_at']);
    }
}
