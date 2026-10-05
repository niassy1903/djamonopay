<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Users2;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertSee('auth-shell')
            ->assertSee('Créez votre compte')
            ->assertSee('linear-gradient');
    }

    public function test_registration_screen_cannot_be_rendered_if_support_is_disabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_register(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'nom' => 'User',
            'prenom' => 'Test',
            'email' => 'TEST@example.com',
            'telephone' => '770000000',
            'adresse' => 'Dakar',
            'date_naissance' => '1990-01-01',
            'numero_identite' => 'ID-TEST-001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::AGENT,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertDatabaseHas('users2', [
            'email' => 'test@example.com',
            'role' => UserRole::CLIENT,
        ]);
        $this->assertSame(UserRole::CLIENT, Users2::where('email', 'test@example.com')->value('role'));
    }
}
