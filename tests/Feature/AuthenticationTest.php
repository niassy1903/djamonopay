<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SystemLogger;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('auth-shell')
            ->assertSee('Connectez-vous')
            ->assertSee('linear-gradient');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->createUser(UserRole::CLIENT);

        $response = $this->post('/login', [
            'email' => strtoupper($user->email),
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard-client'));
        $this->get('/dashboard')->assertRedirect(route('dashboard-client'));
    }

    public function test_login_redirects_each_role_to_an_authorized_dashboard(): void
    {
        foreach ([
            UserRole::CLIENT => 'dashboard-client',
            UserRole::DISTRIBUTEUR => 'dashboard-distributeur',
            UserRole::AGENT => 'index',
        ] as $role => $route) {
            $user = $this->createUser($role);

            $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect(route($route));

            $this->post('/logout');
        }
    }

    public function test_login_does_not_return_a_user_to_another_roles_dashboard(): void
    {
        $client = $this->createUser(UserRole::CLIENT);

        $this->withSession(['url.intended' => route('index')])
            ->post('/login', [
                'email' => $client->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard-client'));
    }

    public function test_login_preserves_an_authorized_qr_payment_destination(): void
    {
        $client = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::DISTRIBUTEUR);
        $paymentUrl = route('payments.new', [
            'recipient_account' => $recipient->comptes()->firstOrFail()->numero_compte,
        ]);

        $this->withSession(['url.intended' => $paymentUrl])
            ->post('/login', [
                'email' => $client->email,
                'password' => 'password',
            ])
            ->assertRedirect($paymentUrl);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = $this->createUser(UserRole::CLIENT);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_disabled_accounts_cannot_authenticate(): void
    {
        $user = $this->createUser(UserRole::CLIENT, false);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_disabling_an_account_ends_its_existing_web_session(): void
    {
        $user = $this->createUser(UserRole::CLIENT);
        $this->actingAs($user);
        $user->update(['etat_compte' => false]);

        $this->get('/dashboard/dashboard-client')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_dashboard_routes_are_restricted_to_the_matching_role(): void
    {
        $this->withoutVite();
        $this->actingAs($this->createUser(UserRole::CLIENT));

        $this->get('/dashboard/dashboard-client')
            ->assertOk()
            ->assertSee('Voir mon profil')
            ->assertSee('Modifier mon profil')
            ->assertSee('Déconnexion');
        $this->get('/dashboard/dashboard-distributeur')->assertForbidden();
        $this->get('/dashboard/index')->assertForbidden();

        $this->actingAs($this->createUser(UserRole::DISTRIBUTEUR));
        $this->get('/dashboard/dashboard-distributeur')->assertOk();
        $this->get('/dashboard/dashboard-client')->assertForbidden();

        $this->actingAs($this->createUser(UserRole::AGENT));
        $this->get('/dashboard/index')->assertOk();
        $this->get('/dashboard/dashboard-client')->assertForbidden();
    }

    public function test_each_role_redirects_to_its_own_dashboard(): void
    {
        foreach ([
            UserRole::CLIENT => 'dashboard-client',
            UserRole::DISTRIBUTEUR => 'dashboard-distributeur',
            UserRole::AGENT => 'index',
        ] as $role => $route) {
            $this->actingAs($this->createUser($role));
            $this->get('/dashboard')->assertRedirect(route($route));
        }
    }

    public function test_agent_dashboards_display_database_users_accounts_transactions_and_activities(): void
    {
        $this->withoutVite();
        $agent = $this->createUser(UserRole::AGENT);
        $client = $this->createUser(UserRole::CLIENT);
        $sourceAccount = $agent->comptes()->firstOrFail();
        $destinationAccount = $client->comptes()->firstOrFail();
        $destinationAccount->update(['solde' => 12500]);

        Transaction::create([
            'reference' => 'TX-DASH-0001',
            'compte_source_id' => $sourceAccount->id,
            'compte_destination_id' => $destinationAccount->id,
            'type' => 'transfert',
            'montant' => 5000,
            'frais' => 100,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Test opération réelle',
            'traitee_at' => now(),
        ]);

        SystemLogger::create([
            'user_id' => $agent->id,
            'action' => 'dashboard.test',
            'description' => 'Activité test depuis la base',
            'adresse_ip' => '127.0.0.1',
        ]);

        $this->actingAs($agent)
            ->get(route('index'))
            ->assertOk()
            ->assertSee('TX-DASH-0001')
            ->assertSee('5 000 XOF')
            ->assertSee('Activité test depuis la base');

        $this->get(route('dashboard-transactions'))
            ->assertOk()
            ->assertSee('TX-DASH-0001')
            ->assertSee($client->name)
            ->assertSee('5 000 XOF');

        $this->get(route('dashboard-activitées'))
            ->assertOk()
            ->assertSee('Activité test depuis la base')
            ->assertSee('dashboard.test');

        $this->get(route('dashboard-utilisateurs', ['role' => UserRole::CLIENT]))
            ->assertOk()
            ->assertSee($client->email)
            ->assertSee('12 500 XOF');
    }

    public function test_only_agents_can_provision_users_for_every_supported_role(): void
    {
        $payload = [
            'nom' => 'Distributor',
            'prenom' => 'Test',
            'email' => 'distributor@example.com',
            'mot_de_passe' => 'password',
            'role' => UserRole::DISTRIBUTEUR,
            'telephone' => '770000001',
            'adresse' => 'Dakar',
            'date_naissance' => '1990-01-01',
            'numero_identite' => 'ID-DIST-001',
        ];

        $this->actingAs($this->createUser(UserRole::CLIENT), 'sanctum')
            ->postJson('/api/users', $payload)
            ->assertForbidden();

        $agent = $this->createUser(UserRole::AGENT);
        $response = $this->actingAs($agent, 'sanctum')
            ->postJson('/api/users', $payload)
            ->assertCreated()
            ->assertJsonPath('data.telephone', '770000001');

        $this->assertDatabaseHas('users2', [
            'email' => 'distributor@example.com',
            'role' => UserRole::DISTRIBUTEUR,
        ]);
        $this->assertDatabaseHas('system_loggers', [
            'user_id' => $agent->id,
            'action' => 'user.created',
        ]);

        $this->getJson('/api/users/'.$response->json('data.id'))
            ->assertOk()
            ->assertJsonPath('data.numero_identite', 'ID-DIST-001');

        $this->actingAs($agent, 'sanctum')
            ->postJson('/api/users', [
                ...$payload,
                'email' => 'client@example.com',
                'role' => UserRole::CLIENT,
                'numero_identite' => 'ID-CLIENT-001',
            ])
            ->assertCreated()
            ->assertJsonPath('data.role', UserRole::CLIENT);

        $this->assertDatabaseHas('users2', [
            'email' => 'client@example.com',
            'role' => UserRole::CLIENT,
        ]);
    }

    private function createUser(string $role, bool $active = true): Users2
    {
        return Users2::create([
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => fake()->unique()->safeEmail(),
            'mot_de_passe' => 'password',
            'role' => $role,
            'telephone' => '770000000',
            'adresse' => 'Dakar',
            'date_naissance' => '1990-01-01',
            'numero_identite' => fake()->unique()->numerify('##########'),
            'etat_compte' => $active,
        ]);
    }
}
