<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndUserSeeder::class);
    }

    /**
     * @return array<string, string>
     */
    private function validRegistration(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Zyon',
            'prefix' => '',
            'last_name' => 'Kolf',
            'email' => 'nieuw@mail.nl',
            'password' => 'geheim123',
            'password_confirmation' => 'geheim123',
        ], $overrides);
    }

    public function test_registration_page_is_shown_to_guests(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Account aanmaken')
            ->assertSee('Registreren');
    }

    public function test_happy_new_user_registers_and_sees_confirmation(): void
    {
        $response = $this->post(route('register.store'), $this->validRegistration());

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Je account is succesvol aangemaakt.');

        $user = User::where('email', 'nieuw@mail.nl')->firstOrFail();
        $this->assertSame('patient', $user->role->name);
        $this->assertSame('Zyon Kolf', $user->name);
        $this->assertNotSame('geheim123', $user->password);

        $this->get(route('login'))->assertSee('Je account is succesvol aangemaakt.');
    }

    public function test_happy_registered_user_can_log_in_afterwards(): void
    {
        $this->post(route('register.store'), $this->validRegistration());

        $this->post(route('login.store'), ['email' => 'nieuw@mail.nl', 'password' => 'geheim123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_unhappy_registration_fails_when_email_already_exists(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register.store'), $this->validRegistration(['email' => 'jan.smit@mail.nl']));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['email' => 'Dit e-mailadres is al in gebruik. Gebruik een ander e-mailadres.']);
        $this->assertSame(1, User::where('email', 'jan.smit@mail.nl')->count());
    }

    public function test_unhappy_existing_email_is_detected_regardless_of_capitals(): void
    {
        $this->post(route('register.store'), $this->validRegistration(['email' => 'Jan.Smit@Mail.nl']))
            ->assertSessionHasErrors('email');
    }

    public function test_unhappy_registration_fails_with_missing_fields_and_short_password(): void
    {
        $this->post(route('register.store'), $this->validRegistration([
            'first_name' => '',
            'password' => 'kort',
            'password_confirmation' => 'kort',
        ]))->assertSessionHasErrors(['first_name', 'password']);

        $this->assertDatabaseMissing(User::class, ['email' => 'nieuw@mail.nl']);
    }

    public function test_logged_in_user_cannot_open_registration(): void
    {
        $this->actingAs(User::first())->get(route('register'))->assertRedirect();
    }
}
