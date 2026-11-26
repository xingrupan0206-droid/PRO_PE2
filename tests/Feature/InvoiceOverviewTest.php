<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InvoiceOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function staffMember(): User
    {
        return User::where('email', 'm.visser@tandarts.nl')->firstOrFail();
    }

    public function test_happy_staff_member_sees_all_invoices_with_required_columns(): void
    {
        $response = $this->actingAs($this->staffMember())->get(route('invoices.index'));

        $response->assertOk()
            ->assertSee('Facturatieoverzicht')
            ->assertSeeInOrder(['Factuurnummer', 'Patiënt', 'Factuurdatum', 'Bedrag', 'Betaalstatus'])
            ->assertSee('F-2026-0001')
            ->assertSee('Jan Smit')
            ->assertSee('10-11-2026')
            ->assertSee('€ 32,50', false)
            ->assertSee('Betaald')
            ->assertSee('F-2026-0002')
            ->assertSee('Openstaand')
            ->assertSee('3 facturen');
    }

    public function test_happy_staff_member_can_search_on_patient_name(): void
    {
        $this->actingAs($this->staffMember())
            ->get(route('invoices.index', ['zoek' => 'Emma']))
            ->assertOk()
            ->assertSee('F-2026-0002')
            ->assertDontSee('F-2026-0001');
    }

    public function test_happy_staff_member_can_filter_on_payment_status(): void
    {
        $this->actingAs($this->staffMember())
            ->get(route('invoices.index', ['status' => 'outstanding']))
            ->assertOk()
            ->assertSee('F-2026-0002')
            ->assertDontSee('F-2026-0003');
    }

    public function test_search_without_results_shows_clear_message(): void
    {
        $this->actingAs($this->staffMember())
            ->get(route('invoices.index', ['zoek' => 'F-9999']))
            ->assertOk()
            ->assertSee('Geen facturen gevonden met deze zoekopdracht.');
    }

    public function test_unhappy_database_error_shows_clear_message_instead_of_overview(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_dashboard_omzet_per_maand');
        Schema::disableForeignKeyConstraints();
        Schema::drop('invoice_lines');
        Schema::drop('payments');
        Schema::drop('invoices');

        $this->actingAs($this->staffMember())
            ->get(route('invoices.index'))
            ->assertOk()
            ->assertSee('Het facturatieoverzicht kan niet worden geladen. Probeer het later opnieuw.')
            ->assertDontSee('Betaalstatus');
    }

    public function test_unhappy_patient_has_no_access_to_invoice_overview(): void
    {
        $this->actingAs(User::where('email', 'jan.smit@mail.nl')->firstOrFail())
            ->get(route('invoices.index'))
            ->assertForbidden()
            ->assertSee('Je hebt onvoldoende rechten om deze pagina te bekijken.');
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get(route('invoices.index'))->assertRedirect(route('login'));
    }
}
