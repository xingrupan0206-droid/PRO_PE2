<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\Afspraak\Read;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AfspraakOverzichtTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_sees_all_appointments_with_date_time_employee_customer_and_status(): void
    {
        $this->seed();
        $employee = User::findOrFail(2);

        $response = $this->actingAs($employee)->get(route('afspraken.overzicht'));

        $response->assertOk()
            ->assertViewIs('afspraken.overzicht')
            ->assertViewHas('afspraken', fn (Collection $afspraken): bool => $afspraken->count() === 7)
            ->assertSee('Datum')
            ->assertSee('Tijdstip')
            ->assertSee('Medewerker')
            ->assertSee('Klant')
            ->assertSee('Status')
            ->assertSee('10-11-2026')
            ->assertSee('09:00')
            ->assertSee('Pieter Jansen')
            ->assertSee('Jan Smit')
            ->assertSee('afgerond')
            ->assertSee('02-12-2026')
            ->assertSee('bevestigd');
    }

    public function test_database_failure_shows_error_message_without_overview(): void
    {
        $this->seed();
        $employee = User::findOrFail(2);

        config([
            'database.connections.unavailable' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 1,
                'database' => 'Tandarts',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ]);
        $this->app->instance(Read::class, new Read('unavailable'));

        $this->actingAs($employee)
            ->get(route('afspraken.overzicht'))
            ->assertOk()
            ->assertSee('Momenteel kunnen er geen afspraken worden gevonden. Probeer het later opnieuw.')
            ->assertDontSee('Tijdstip');
    }

    public function test_status_filter_only_shows_appointments_with_the_selected_status(): void
    {
        $this->seed();
        $employee = User::findOrFail(2);

        $response = $this->actingAs($employee)
            ->get(route('afspraken.overzicht', ['status' => 'cancelled']));

        $response->assertOk()
            ->assertViewHas('statusFilter', 'cancelled')
            ->assertViewHas('afspraken', fn (Collection $afspraken): bool => $afspraken->count() === 1
                && $afspraken->first()['status'] === 'geannuleerd')
            ->assertSee('30-11-2026');
    }

    public function test_unknown_status_filter_shows_all_appointments(): void
    {
        $this->seed();
        $employee = User::findOrFail(2);

        $this->actingAs($employee)
            ->get(route('afspraken.overzicht', ['status' => 'onbekend']))
            ->assertOk()
            ->assertViewHas('statusFilter', null)
            ->assertViewHas('afspraken', fn (Collection $afspraken): bool => $afspraken->count() === 7);
    }

    public function test_database_failure_during_authentication_shows_message_instead_of_error_page(): void
    {
        $this->seed();
        $user = User::findOrFail(2);
        $sessieSleutel = Auth::guard()->getName();
        $standaardVerbinding = config('database.default');

        config([
            'database.default' => 'unavailable',
            'database.connections.unavailable' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 1,
                'database' => 'Tandarts',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ]);
        DB::purge('unavailable');

        try {
            $this->withSession([$sessieSleutel => $user->getAuthIdentifier()])
                ->get(route('afspraken.overzicht'))
                ->assertOk()
                ->assertSee(Read::FOUTMELDING)
                ->assertDontSee('Tijdstip');
        } finally {
            config(['database.default' => $standaardVerbinding]);
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('afspraken.overzicht'))
            ->assertRedirect(route('login'));
    }

    public function test_patient_cannot_open_the_overview(): void
    {
        $this->seed();
        $patient = User::findOrFail(6);

        $this->actingAs($patient)
            ->get(route('afspraken.overzicht'))
            ->assertForbidden();
    }
}
