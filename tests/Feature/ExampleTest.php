<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_login_screen_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('HomeStock IMS');
    }

    public function test_authenticated_dashboard_returns_success(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@demo.test',
            'role' => \App\Models\User::ROLE_ADMIN,
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }
}
