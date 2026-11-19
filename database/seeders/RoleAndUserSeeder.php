<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'admin', 'description' => 'Praktijkmanagement en systeembeheer', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'tandarts', 'description' => 'Behandelaar, beheert agenda en dossiers', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'mondhygienist', 'description' => 'Gebitsreiniging en preventie', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'assistent', 'description' => 'Ondersteunt balie, dossiers en afspraken', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'patient', 'description' => 'Patiënt van de praktijk', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'updated_at']);

        $password = Hash::make('wachtwoord');
        $accounts = [
            [1, 1, 'admin@tandarts.nl', 'Sanne', 'de', 'Vries', '0612345601'],
            [2, 2, 'p.jansen@tandarts.nl', 'Pieter', null, 'Jansen', '0612345602'],
            [3, 2, 'f.elamrani@tandarts.nl', 'Fatima', 'el', 'Amrani', '0612345603'],
            [4, 3, 'l.bakker@tandarts.nl', 'Lotte', null, 'Bakker', '0612345604'],
            [5, 4, 'm.visser@tandarts.nl', 'Mark', null, 'Visser', '0612345605'],
            [6, 5, 'jan.smit@mail.nl', 'Jan', null, 'Smit', '0623456701'],
            [7, 5, 'emma.mulder@mail.nl', 'Emma', null, 'Mulder', '0623456702'],
            [8, 5, 'ahmed.yilmaz@mail.nl', 'Ahmed', null, 'Yilmaz', '0623456703'],
            [9, 5, 'sophie.vandijk@mail.nl', 'Sophie', 'van', 'Dijk', '0623456704'],
            [10, 5, 'daan.meijer@mail.nl', 'Daan', null, 'Meijer', '0623456705'],
        ];

        $users = array_map(function (array $account) use ($password, $now): array {
            [$id, $roleId, $email, $firstName, $prefix, $lastName, $phone] = $account;

            return [
                'id' => $id,
                'role_id' => $roleId,
                'name' => trim(implode(' ', array_filter([$firstName, $prefix, $lastName]))),
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'prefix' => $prefix,
                'last_name' => $lastName,
                'phone' => $phone,
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $accounts);

        DB::table('users')->upsert($users, ['email'], [
            'role_id',
            'name',
            'password',
            'first_name',
            'prefix',
            'last_name',
            'phone',
            'is_active',
            'email_verified_at',
            'updated_at',
        ]);
    }
}
