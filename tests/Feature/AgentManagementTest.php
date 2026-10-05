<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SystemLogger;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_create_client_distributor_and_another_agent(): void
    {
        $agent = $this->createUser(UserRole::AGENT);

        foreach ([UserRole::CLIENT, UserRole::DISTRIBUTEUR, UserRole::AGENT] as $index => $role) {
            $email = $role.'-'.$index.'@example.test';

            $this->actingAs($agent)
                ->post(route('agent.users.store'), [
                    'prenom' => 'Nouveau',
                    'nom' => ucfirst($role),
                    'email' => $email,
                    'role' => $role,
                    'telephone' => '77000000'.$index,
                    'adresse' => 'Dakar',
                    'date_naissance' => '1990-01-01',
                    'numero_identite' => 'IDENTITY-'.$index,
                    'mot_de_passe' => 'secret-pass',
                ])
                ->assertRedirect(route('dashboard-utilisateurs'))
                ->assertSessionHas('operation_message');

            $created = Users2::where('email', $email)->firstOrFail();
            $this->assertSame($role, $created->role);
            $this->assertTrue(Hash::check('secret-pass', $created->mot_de_passe));
            $this->assertDatabaseHas('comptes', [
                'user_id' => $created->id,
                'devise' => 'XOF',
                'statut' => 'actif',
            ]);
        }

        $this->assertSame(3, SystemLogger::where('user_id', $agent->id)->where('action', 'user.created')->count());
    }

    public function test_only_agents_can_create_accounts_from_the_dashboard(): void
    {
        $this->actingAs($this->createUser(UserRole::DISTRIBUTEUR))
            ->post(route('agent.users.store'), [])
            ->assertForbidden();
    }

    public function test_user_search_filters_by_full_name_account_number_role_and_status(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $client = $this->createUser(UserRole::CLIENT);
        $client->update(['prenom' => 'Awa', 'nom' => 'Diallo', 'telephone' => '771234567']);
        $client->comptes()->firstOrFail()->update(['numero_compte' => 'DP0000008765']);
        $disabledDistributor = $this->createUser(UserRole::DISTRIBUTEUR, false);

        $this->actingAs($agent)
            ->get(route('dashboard-utilisateurs', ['search' => 'Awa Diallo', 'role' => UserRole::CLIENT, 'status' => 'active']))
            ->assertOk()
            ->assertSee($client->email)
            ->assertDontSee($disabledDistributor->email);

        $this->get(route('dashboard-utilisateurs', ['search' => 'DP0000008765']))
            ->assertOk()
            ->assertSee($client->email);

        $this->get(route('dashboard-utilisateurs', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee($disabledDistributor->email)
            ->assertDontSee($client->email);
    }

    public function test_agent_can_update_a_user_without_replacing_an_empty_password(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $client = $this->createUser(UserRole::CLIENT);
        $originalPassword = $client->mot_de_passe;

        $this->actingAs($agent)
            ->put(route('agent.users.update', $client), [
                'prenom' => 'Aminata',
                'nom' => $client->nom,
                'email' => $client->email,
                'role' => UserRole::CLIENT,
                'telephone' => $client->telephone,
                'adresse' => $client->adresse,
                'date_naissance' => '1990-01-01',
                'numero_identite' => $client->numero_identite,
                'mot_de_passe' => '',
                'etat_compte' => '0',
                'modal' => 'edit',
                'user_id' => $client->id,
            ])
            ->assertRedirect(route('dashboard-utilisateurs'))
            ->assertSessionHas('operation_message');

        $client->refresh();
        $this->assertSame('Aminata', $client->prenom);
        $this->assertFalse($client->etat_compte);
        $this->assertSame($originalPassword, $client->mot_de_passe);
        $this->assertDatabaseHas('system_loggers', [
            'user_id' => $agent->id,
            'action' => 'user.updated',
            'subject_id' => $client->id,
        ]);
    }

    public function test_agent_can_delete_only_a_user_without_balance_or_transaction_history(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $safeUser = $this->createUser(UserRole::CLIENT);
        $fundedUser = $this->createUser(UserRole::CLIENT);
        $historicalUser = $this->createUser(UserRole::CLIENT);
        $fundedUser->comptes()->firstOrFail()->update(['solde' => 500]);
        $historicalAccount = $historicalUser->comptes()->firstOrFail();
        $otherAccount = $this->createUser(UserRole::DISTRIBUTEUR)->comptes()->firstOrFail();
        Transaction::create([
            'reference' => 'USER-DELETE-HISTORY-001',
            'actor_id' => $historicalUser->id,
            'compte_source_id' => $historicalAccount->id,
            'compte_destination_id' => $otherAccount->id,
            'type' => 'transfert',
            'montant' => 1,
            'frais' => 0,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Historique à préserver',
        ]);

        $this->actingAs($agent)
            ->delete(route('agent.users.destroy', $safeUser))
            ->assertRedirect(route('dashboard-utilisateurs'))
            ->assertSessionHas('operation_message');

        $this->assertDatabaseMissing('users2', ['id' => $safeUser->id]);
        $this->assertDatabaseHas('system_loggers', [
            'user_id' => $agent->id,
            'action' => 'user.deleted',
            'subject_id' => $safeUser->id,
        ]);

        $this->delete(route('agent.users.destroy', $fundedUser))
            ->assertRedirect()
            ->assertSessionHasErrors('user');
        $this->delete(route('agent.users.destroy', $historicalUser))
            ->assertRedirect()
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users2', ['id' => $fundedUser->id]);
        $this->assertDatabaseHas('users2', ['id' => $historicalUser->id]);
    }

    public function test_agent_cannot_modify_or_delete_their_own_account_from_the_user_directory(): void
    {
        $agent = $this->createUser(UserRole::AGENT);

        $this->actingAs($agent)
            ->put(route('agent.users.update', $agent), ['prenom' => 'Changed'])
            ->assertStatus(422);

        $this->delete(route('agent.users.destroy', $agent))
            ->assertStatus(422);
    }

    public function test_transaction_search_filters_by_reference_status_type_party_and_dates(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $client = $this->createUser(UserRole::CLIENT);
        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);

        $transaction = Transaction::create([
            'reference' => 'PAY-SEARCH-001',
            'actor_id' => $client->id,
            'compte_source_id' => $client->comptes()->firstOrFail()->id,
            'compte_destination_id' => $distributor->comptes()->firstOrFail()->id,
            'type' => 'paiement_qr',
            'montant' => 5000,
            'frais' => 100,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Paiement test',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        Transaction::create([
            'reference' => 'DEP-OTHER-002',
            'actor_id' => $distributor->id,
            'compte_source_id' => $distributor->comptes()->firstOrFail()->id,
            'compte_destination_id' => $client->comptes()->firstOrFail()->id,
            'type' => 'depot',
            'montant' => 2000,
            'frais' => 0,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Dépôt test',
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        $this->actingAs($agent)
            ->get(route('dashboard-transactions', [
                'search' => 'PAY-SEARCH-001',
                'type' => 'paiement_qr',
                'status' => 'terminee',
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($transaction->reference)
            ->assertDontSee('DEP-OTHER-002');

        $this->get(route('dashboard-transactions', ['search' => $client->email]))
            ->assertOk()
            ->assertSee($transaction->reference)
            ->assertSee('DEP-OTHER-002');
    }

    public function test_activity_search_filters_by_action_details_actor_and_dates(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $client = $this->createUser(UserRole::CLIENT);

        SystemLogger::create([
            'user_id' => $client->id,
            'action' => 'wallet.test.deposit',
            'description' => 'Dépôt de démonstration enregistré',
            'adresse_ip' => '192.0.2.10',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);
        SystemLogger::create([
            'user_id' => $agent->id,
            'action' => 'profile.updated',
            'description' => 'Mise à jour du profil agent',
            'adresse_ip' => '192.0.2.20',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $this->actingAs($agent)
            ->get(route('dashboard-activitées', [
                'search' => 'démonstration',
                'start_date' => now()->subDays(2)->toDateString(),
                'end_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Dépôt de démonstration enregistré')
            ->assertDontSee('Mise à jour du profil agent');

        $this->get(route('dashboard-activitées', ['search' => '192.0.2.10']))
            ->assertOk()
            ->assertSee('wallet.test.deposit')
            ->assertDontSee('profile.updated');
    }

    private function createUser(string $role, bool $active = true): Users2
    {
        return Users2::create([
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'mot_de_passe' => 'password',
            'role' => $role,
            'telephone' => fake()->numerify('77#######'),
            'adresse' => 'Dakar',
            'date_naissance' => '1990-01-01',
            'numero_identite' => fake()->unique()->numerify('##########'),
            'etat_compte' => $active,
        ]);
    }
}
