<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_happy_user_logs_out_and_is_sent_to_the_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Je bent succesvol uitgelogd.');
        $this->assertGuest();
        $this->assertDatabaseHas(AuditLog::class, ['user_id' => $user->id, 'action' => 'logout']);
    }

    public function test_unhappy_logout_fails_on_database_error_and_user_stays_logged_in(): void
    {
        $user = User::factory()->create();
        Schema::drop('audit_logs');

        $response = $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('logout'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Uitloggen is niet gelukt door een databasefout. Probeer het opnieuw.');
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_cannot_use_logout(): void
    {
        $this->post(route('logout'))->assertRedirect(route('login'));
    }
}
