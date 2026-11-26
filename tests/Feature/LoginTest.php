<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_happy_user_logs_in_with_correct_credentials_and_lands_on_the_dashboard(): void
    {
        $user = User::factory()->create(['email' => 'zyon@smilepro.nl']);

        $response = $this->post(route('login.store'), [
            'email' => 'zyon@smilepro.nl',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_unhappy_login_fails_with_wrong_password_and_shows_clear_message(): void
    {
        User::factory()->create(['email' => 'zyon@smilepro.nl']);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'zyon@smilepro.nl',
            'password' => 'verkeerd-wachtwoord',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email' => 'Het e-mailadres of wachtwoord is onjuist.']);
        $this->assertGuest();
    }

    public function test_unhappy_login_fails_with_unknown_email(): void
    {
        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'bestaat.niet@smilepro.nl',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'Het e-mailadres of wachtwoord is onjuist.']);
        $this->assertGuest();
    }

    public function test_unhappy_inactive_account_cannot_log_in(): void
    {
        User::factory()->create(['email' => 'inactief@smilepro.nl', 'is_active' => false]);

        $this->post(route('login.store'), [
            'email' => 'inactief@smilepro.nl',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_error_message_is_visible_on_the_login_page(): void
    {
        $this->followingRedirects()
            ->from(route('login'))
            ->post(route('login.store'), ['email' => 'fout@smilepro.nl', 'password' => 'fout'])
            ->assertSee('Het e-mailadres of wachtwoord is onjuist.');
    }
}
