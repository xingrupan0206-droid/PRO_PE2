<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_homepage_displays_the_welcome_page_for_guests(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('welcome');
    }

    public function test_the_homepage_displays_the_welcome_page_for_authenticated_users(): void
    {
        $this->actingAs(User::factory()->make())
            ->get('/')
            ->assertOk()
            ->assertViewIs('welcome');
    }
}
