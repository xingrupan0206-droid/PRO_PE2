<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientAndEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('employees')->upsert([
            ['id' => 1, 'user_id' => 2, 'function' => 'tandarts', 'big_number' => '19012345601', 'specialization' => 'Algemene tandheelkunde', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'user_id' => 3, 'function' => 'tandarts', 'big_number' => '19012345602', 'specialization' => 'Endodontologie', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'user_id' => 4, 'function' => 'mondhygienist', 'big_number' => '29012345603', 'specialization' => 'Preventie en parodontologie', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'user_id' => 5, 'function' => 'assistent', 'big_number' => null, 'specialization' => 'Baliewerk en stoelassistentie', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['user_id', 'function', 'big_number', 'specialization', 'updated_at']);

        DB::table('patients')->upsert([
            ['id' => 1, 'user_id' => 6, 'birth_date' => '1985-03-12', 'street' => 'Kerkstraat', 'house_number' => '12', 'postal_code' => '3511 AB', 'city' => 'Utrecht', 'insurance_company' => 'Zilveren Kruis', 'policy_number' => 'ZK1234567', 'avg_consent' => true, 'avg_consent_at' => '2026-01-10 10:00:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'user_id' => 7, 'birth_date' => '1992-07-25', 'street' => 'Lindelaan', 'house_number' => '4A', 'postal_code' => '3581 CD', 'city' => 'Utrecht', 'insurance_company' => 'CZ', 'policy_number' => 'CZ7654321', 'avg_consent' => true, 'avg_consent_at' => '2026-02-03 14:30:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'user_id' => 8, 'birth_date' => '1978-11-02', 'street' => 'Maanstraat', 'house_number' => '88', 'postal_code' => '3531 EF', 'city' => 'Utrecht', 'insurance_company' => 'VGZ', 'policy_number' => 'VG1122334', 'avg_consent' => true, 'avg_consent_at' => '2026-03-15 09:15:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'user_id' => 9, 'birth_date' => '2001-05-30', 'street' => 'Dorpsweg', 'house_number' => '7', 'postal_code' => '3454 GH', 'city' => 'De Meern', 'insurance_company' => 'Menzis', 'policy_number' => 'MZ5566778', 'avg_consent' => true, 'avg_consent_at' => '2026-04-21 16:45:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'user_id' => 10, 'birth_date' => '1969-09-18', 'street' => 'Stationsplein', 'house_number' => '101', 'postal_code' => '3511 JK', 'city' => 'Utrecht', 'insurance_company' => 'Zilveren Kruis', 'policy_number' => 'ZK9988776', 'avg_consent' => true, 'avg_consent_at' => '2026-05-02 11:00:00', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['user_id', 'birth_date', 'street', 'house_number', 'postal_code', 'city', 'insurance_company', 'policy_number', 'avg_consent', 'avg_consent_at', 'updated_at']);

        DB::table('medical_records')->upsert([
            ['id' => 1, 'patient_id' => 1, 'allergies' => 'Penicilline', 'medication' => null, 'conditions' => null, 'notes' => 'Gevoelig gebit bij koude dranken.', 'updated_by' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'patient_id' => 2, 'allergies' => null, 'medication' => null, 'conditions' => null, 'notes' => 'Regelmatige controle, geen bijzonderheden.', 'updated_by' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'patient_id' => 3, 'allergies' => 'Latex', 'medication' => 'Bloedverdunner', 'conditions' => 'Diabetes type 2', 'notes' => 'Voorzichtig bij extracties.', 'updated_by' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'patient_id' => 4, 'allergies' => null, 'medication' => null, 'conditions' => null, 'notes' => 'Draagt beugel (vast).', 'updated_by' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'patient_id' => 5, 'allergies' => 'Lidocaïne', 'medication' => 'Bloeddrukverlager', 'conditions' => 'Hoge bloeddruk', 'notes' => 'Tandvleesontsteking in behandeling.', 'updated_by' => 2, 'created_at' => $now, 'updated_at' => $now],
        ], ['patient_id'], ['allergies', 'medication', 'conditions', 'notes', 'updated_by', 'updated_at']);
    }
}
