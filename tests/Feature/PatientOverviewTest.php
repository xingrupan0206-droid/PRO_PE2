<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function userWithEmail(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function test_happy_staff_member_sees_all_patients_with_name_birth_date_and_phone(): void
    {
        $response = $this->actingAs($this->userWithEmail('m.visser@tandarts.nl'))
            ->get(route('patients.index'));

        $response->assertOk()
            ->assertSee('Patiëntenoverzicht')
            ->assertSee('Jan Smit')
            ->assertSee('12-03-1985')
            ->assertSee('0623456701')
            ->assertSee('Sophie van Dijk')
            ->assertSee('5 patiënten');
    }

    public function test_happy_admin_and_dentist_can_open_the_overview(): void
    {
        $this->actingAs($this->userWithEmail('admin@tandarts.nl'))
            ->get(route('patients.index'))
            ->assertOk();

        $this->actingAs($this->userWithEmail('p.jansen@tandarts.nl'))
            ->get(route('patients.index'))
            ->assertOk();
    }

    public function test_happy_staff_member_can_search_patients(): void
    {
        $this->actingAs($this->userWithEmail('m.visser@tandarts.nl'))
            ->get(route('patients.index', ['zoek' => 'Mulder']))
            ->assertOk()
            ->assertSee('Emma Mulder')
            ->assertDontSee('Jan Smit');
    }

    public function test_search_without_results_shows_clear_message(): void
    {
        $this->actingAs($this->userWithEmail('m.visser@tandarts.nl'))
            ->get(route('patients.index', ['zoek' => 'Onbekend']))
            ->assertOk()
            ->assertSee('Geen patiënten gevonden voor');
    }

    public function test_unhappy_patient_has_no_access_and_sees_insufficient_rights_message(): void
    {
        $this->actingAs($this->userWithEmail('jan.smit@mail.nl'))
            ->get(route('patients.index'))
            ->assertForbidden()
            ->assertSee('Je hebt onvoldoende rechten om deze pagina te bekijken.')
            ->assertDontSee('Emma Mulder');
    }

    public function test_unhappy_user_without_role_has_no_access(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('patients.index'))
            ->assertForbidden();
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get(route('patients.index'))->assertRedirect(route('login'));
    }
}
