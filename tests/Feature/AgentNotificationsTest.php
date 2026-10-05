<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AgentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_wallet_transaction_notifies_every_active_agent_and_can_be_fetched_once(): void
    {
        $agents = collect([
            $this->createUser(UserRole::AGENT),
            $this->createUser(UserRole::AGENT),
            $this->createUser(UserRole::AGENT),
        ]);
        $client = $this->createUser(UserRole::CLIENT);

        Transaction::create([
            'reference' => 'DEP-'.strtoupper((string) Str::ulid()),
            'actor_id' => $client->id,
            'compte_destination_id' => $client->comptes()->firstOrFail()->id,
            'type' => 'depot',
            'montant' => 5000,
            'frais' => 0,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Dépôt de test.',
            'traitee_at' => now(),
        ]);

        $this->assertDatabaseCount('notifications', 4);
        foreach ($agents as $agent) {
            $this->assertDatabaseHas('notifications', [
                'notifiable_id' => $agent->id,
                'notifiable_type' => Users2::class,
            ]);
        }

        $this->actingAs($agents->first())
            ->get(route('index'))
            ->assertOk()
            ->assertSee('Activer le son et les alertes navigateur')
            ->assertSee('Notification.requestPermission', false);

        $this->actingAs($agents->first())
            ->getJson(route('agent.notifications.index'))
            ->assertOk()
            ->assertJsonPath('notifications.0.data.title', 'Nouveau Dépôt')
            ->assertJsonPath('notifications.0.data.reference', Transaction::firstOrFail()->reference);

        $this->getJson(route('agent.notifications.index'))
            ->assertOk()
            ->assertExactJson(['notifications' => []]);
    }

    public function test_non_transaction_records_and_inactive_agents_do_not_receive_wallet_alerts(): void
    {
        $activeAgent = $this->createUser(UserRole::AGENT);
        $inactiveAgent = $this->createUser(UserRole::AGENT);
        $inactiveAgent->update(['etat_compte' => false]);
        $client = $this->createUser(UserRole::CLIENT);

        Transaction::create([
            'reference' => 'CRD-'.strtoupper((string) Str::ulid()),
            'actor_id' => $client->id,
            'compte_destination_id' => $client->comptes()->firstOrFail()->id,
            'type' => 'credit_agent',
            'montant' => 5000,
            'frais' => 0,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Crédit agent de test.',
            'traitee_at' => now(),
        ]);

        $this->assertDatabaseCount('notifications', 0);

        Transaction::create([
            'reference' => 'DEP-'.strtoupper((string) Str::ulid()),
            'actor_id' => $client->id,
            'compte_destination_id' => $client->comptes()->firstOrFail()->id,
            'type' => 'depot',
            'montant' => 5000,
            'frais' => 0,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Dépôt de test.',
            'traitee_at' => now(),
        ]);

        $this->assertDatabaseCount('notifications', 2);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $activeAgent->id,
            'notifiable_type' => Users2::class,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $inactiveAgent->id,
            'notifiable_type' => Users2::class,
        ]);
    }

    public function test_authenticated_clients_can_use_their_notification_bell(): void
    {
        $client = $this->createUser(UserRole::CLIENT);

        $this->actingAs($client)
            ->getJson(route('agent.notifications.index'))
            ->assertOk()
            ->assertExactJson(['notifications' => []]);

        $this->get(route('dashboard-client'))
            ->assertOk()
            ->assertSee('data-agent-notifications', false)
            ->assertSee('data-notification-toggle', false);
    }

    private function createUser(string $role): Users2
    {
        return Users2::create([
            'nom' => 'Test',
            'prenom' => 'Agent',
            'email' => fake()->unique()->safeEmail(),
            'mot_de_passe' => 'password',
            'role' => $role,
            'telephone' => fake()->numerify('77#######'),
            'adresse' => 'Dakar',
            'date_naissance' => '1990-01-01',
            'numero_identite' => fake()->unique()->numerify('##########'),
            'etat_compte' => true,
        ]);
    }
}
