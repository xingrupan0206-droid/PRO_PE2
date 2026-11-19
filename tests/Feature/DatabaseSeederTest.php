<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_accounts_can_authenticate_and_have_their_roles(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@tandarts.nl')->firstOrFail();

        $this->assertSame('admin', $user->role->name);
        $this->assertSame('Sanne de Vries', $user->name);
        $this->get(route('login'))->assertOk();

        $this->post(route('login.store'), [
            'email' => 'admin@tandarts.nl',
            'password' => 'wachtwoord',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk()->assertSee('Sanne de Vries');
    }

    public function test_seeders_load_the_sql_sample_data_and_dashboard_views(): void
    {
        $this->seed();

        $this->assertSame(5, DB::table('roles')->count());
        $this->assertSame(10, DB::table('users')->count());
        $this->assertSame(5, DB::table('patients')->count());
        $this->assertSame(7, DB::table('appointments')->count());
        $this->assertSame(3, DB::table('invoices')->count());
        $this->assertSame(13, DB::table('availabilities')->count());
        $this->assertSame(7, DB::table('v_dashboard_populaire_behandelingen')->count());
    }
}
