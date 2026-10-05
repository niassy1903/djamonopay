<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WalletOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_credit_funds_a_distributor_and_records_the_operator(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);
        $account = $distributor->comptes()->firstOrFail();

        $this->actingAs($agent)
            ->post(route('agent.distributors.credit'), [
                'distributor_account' => $account->id,
                'amount' => 50000,
                'credit_token' => (string) Str::uuid(),
            ])
            ->assertRedirect(route('index'));

        $this->assertSame('50000.00', $account->fresh()->solde);
        $this->assertDatabaseHas('transactions', [
            'compte_source_id' => null,
            'compte_destination_id' => $account->id,
            'actor_id' => $agent->id,
            'type' => 'credit_agent',
            'montant' => 50000,
            'statut' => 'terminee',
        ]);
    }

    public function test_distributor_deposit_credits_client_and_awards_one_percent_bonus(): void
    {
        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);
        $client = $this->createUser(UserRole::CLIENT);
        $distributorAccount = $distributor->comptes()->firstOrFail();
        $clientAccount = $client->comptes()->firstOrFail();
        $distributorAccount->update(['solde' => 20000]);

        $this->actingAs($distributor)
            ->post(route('distributor.deposits.store'), [
                'client_account' => $clientAccount->numero_compte,
                'amount' => 5000,
                'deposit_token' => (string) Str::uuid(),
            ])
            ->assertRedirect(route('dashboard-distributeur'));

        $this->assertSame('15050.00', $distributorAccount->fresh()->solde);
        $this->assertSame('5000.00', $clientAccount->fresh()->solde);
        $this->assertDatabaseHas('transactions', [
            'compte_source_id' => $distributorAccount->id,
            'compte_destination_id' => $clientAccount->id,
            'actor_id' => $distributor->id,
            'type' => 'depot',
            'montant' => 5000,
            'statut' => 'terminee',
        ]);
        $this->assertDatabaseHas('transactions', [
            'compte_destination_id' => $distributorAccount->id,
            'type' => 'bonus_distributeur',
            'montant' => 50,
            'statut' => 'terminee',
        ]);
    }

    public function test_cash_withdrawal_reserves_funds_until_distributor_confirms_cash_handover(): void
    {
        $client = $this->createUser(UserRole::CLIENT);
        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);
        $clientAccount = $client->comptes()->firstOrFail();
        $distributorAccount = $distributor->comptes()->firstOrFail();
        $clientAccount->update(['solde' => 12000]);
        $withdrawalToken = (string) Str::uuid();

        $this->actingAs($client)
            ->post(route('payments.store'), [
                'operation' => 'retrait',
                'recipient_account' => $distributorAccount->numero_compte,
                'amount' => 10000,
                'payment_token' => $withdrawalToken,
            ])
            ->assertRedirect(route('payments.new', ['operation' => 'retrait']))
            ->assertSessionHas('withdrawal_pending', true);

        $withdrawal = Transaction::where('idempotency_key', $withdrawalToken)->firstOrFail();
        $this->assertSame('en_attente', $withdrawal->statut);
        $this->assertSame('200.00', $withdrawal->frais);
        $this->assertSame('12000.00', $clientAccount->fresh()->solde);

        $this->actingAs($client)
            ->get(route('dashboard-client'))
            ->assertOk()
            ->assertSee('2 000')
            ->assertSee('Retrait')
            ->assertSee('en attente');

        $this->actingAs($distributor)
            ->get(route('dashboard-distributeur'))
            ->assertOk()
            ->assertSee('Retraits à confirmer')
            ->assertSee('Espèces remises');

        $this->post(route('payments.store'), [
            'operation' => 'paiement',
            'recipient_account' => $this->createUser(UserRole::CLIENT)->comptes()->firstOrFail()->numero_compte,
            'amount' => 3000,
            'payment_token' => (string) Str::uuid(),
        ])->assertSessionHasErrors('amount');

        $this->actingAs($distributor)
            ->post(route('distributor.withdrawals.complete', $withdrawal))
            ->assertRedirect(route('dashboard-distributeur'));

        $this->assertSame('2000.00', $clientAccount->fresh()->solde);
        $this->assertSame('9900.00', $distributorAccount->fresh()->solde);
        $this->assertSame('terminee', $withdrawal->fresh()->statut);
        $this->assertSame($distributor->id, $withdrawal->fresh()->processed_by_user_id);
        $this->assertDatabaseHas('transactions', [
            'parent_transaction_id' => $withdrawal->id,
            'type' => 'bonus_distributeur',
            'montant' => 100,
            'statut' => 'terminee',
        ]);

        $this->post(route('distributor.withdrawals.complete', $withdrawal));
        $this->assertSame('9900.00', $distributorAccount->fresh()->solde);
        $this->assertSame(1, Transaction::where('type', 'bonus_distributeur')->count());
    }

    public function test_distributor_can_refuse_a_pending_withdrawal_without_moving_funds(): void
    {
        $client = $this->createUser(UserRole::CLIENT);
        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);
        $clientAccount = $client->comptes()->firstOrFail();
        $distributorAccount = $distributor->comptes()->firstOrFail();
        $clientAccount->update(['solde' => 5000]);

        $this->actingAs($client)->post(route('payments.store'), [
            'operation' => 'retrait',
            'recipient_account' => $distributorAccount->numero_compte,
            'amount' => 4000,
            'payment_token' => (string) Str::uuid(),
        ]);
        $withdrawal = Transaction::where('type', 'retrait')->firstOrFail();

        $this->actingAs($distributor)
            ->post(route('distributor.transactions.cancel', $withdrawal))
            ->assertRedirect();

        $this->assertSame('5000.00', $clientAccount->fresh()->solde);
        $this->assertSame('0.00', $distributorAccount->fresh()->solde);
        $this->assertSame('annulee', $withdrawal->fresh()->statut);

        $this->actingAs($client)
            ->get(route('dashboard-client'))
            ->assertOk()
            ->assertSee('5 000');
    }

    public function test_agent_cancellation_reverses_a_completed_client_transfer_without_creating_a_negative_balance(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $sender = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::CLIENT);
        $senderAccount = $sender->comptes()->firstOrFail();
        $recipientAccount = $recipient->comptes()->firstOrFail();
        $senderAccount->update(['solde' => 0]);
        $recipientAccount->update(['solde' => 100]);
        $transaction = Transaction::create([
            'reference' => 'PAY-CANCEL-001',
            'actor_id' => $sender->id,
            'compte_source_id' => $senderAccount->id,
            'compte_destination_id' => $recipientAccount->id,
            'type' => 'paiement_qr',
            'montant' => 10000,
            'frais' => 200,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'traitee_at' => now(),
        ]);

        $this->actingAs($agent)
            ->post(route('agent.transactions.cancel', $transaction))
            ->assertRedirect();

        $this->assertSame('10000.00', $senderAccount->fresh()->solde);
        $this->assertSame('0.00', $recipientAccount->fresh()->solde);
        $this->assertSame('annulee', $transaction->fresh()->statut);
        $this->assertDatabaseHas('transactions', [
            'parent_transaction_id' => $transaction->id,
            'type' => 'remboursement_annulation',
            'statut' => 'terminee',
        ]);
        $this->assertDatabaseHas('system_loggers', [
            'user_id' => $agent->id,
            'action' => 'wallet.transaction.reversed',
        ]);

        $this->post(route('agent.transactions.cancel', $transaction));
        $this->assertSame(2, Transaction::where('reference', 'PAY-CANCEL-001')->orWhere('parent_transaction_id', $transaction->id)->count());
        $this->assertSame('0.00', $recipientAccount->fresh()->solde);
    }

    public function test_distributor_cancellation_reverses_the_customer_deposit_and_claws_back_bonus(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);
        $client = $this->createUser(UserRole::CLIENT);
        $distributorAccount = $distributor->comptes()->firstOrFail();
        $clientAccount = $client->comptes()->firstOrFail();
        $distributorAccount->update(['solde' => 20000]);

        $this->actingAs($distributor)->post(route('distributor.deposits.store'), [
            'client_account' => $clientAccount->numero_compte,
            'amount' => 5000,
            'deposit_token' => (string) Str::uuid(),
        ]);
        $deposit = Transaction::where('type', 'depot')->firstOrFail();
        $clientAccount->update(['solde' => 500]);

        $this->post(route('distributor.transactions.cancel', $deposit))->assertRedirect();

        $this->assertSame('20000.00', $distributorAccount->fresh()->solde);
        $this->assertSame('0.00', $clientAccount->fresh()->solde);
        $this->assertSame('annulee', $deposit->fresh()->statut);
        $this->assertSame('annulee', Transaction::where('parent_transaction_id', $deposit->id)->where('type', 'bonus_distributeur')->firstOrFail()->statut);
        $this->assertDatabaseHas('transactions', [
            'actor_id' => $distributor->id,
            'type' => 'annulation_bonus',
            'montant' => 50,
        ]);

        $this->actingAs($agent)->post(route('agent.transactions.cancel', $deposit));
        $this->assertSame('0.00', $clientAccount->fresh()->solde);
    }

    public function test_client_must_request_support_cancellation_after_fifteen_minutes_with_matching_facts(): void
    {
        $agent = $this->createUser(UserRole::AGENT);
        $sender = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::CLIENT);
        $senderAccount = $sender->comptes()->firstOrFail();
        $recipientAccount = $recipient->comptes()->firstOrFail();
        $senderAccount->update(['solde' => 1000]);
        $recipientAccount->update(['solde' => 500]);
        $transaction = Transaction::create([
            'reference' => 'PAY-SUPPORT-001',
            'actor_id' => $sender->id,
            'compte_source_id' => $senderAccount->id,
            'compte_destination_id' => $recipientAccount->id,
            'type' => 'paiement_qr',
            'montant' => 10000,
            'frais' => 200,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'traitee_at' => now()->subMinutes(16),
        ]);
        $transaction->created_at = now()->subMinutes(16);
        $transaction->save();

        $this->actingAs($sender)
            ->get(route('dashboard-client'))
            ->assertOk()
            ->assertSee('Confirmez les informations exactes');

        $this->post(route('client.transactions.cancel', $transaction))
            ->assertSessionHasErrors('transaction');
        $this->assertSame('terminee', $transaction->fresh()->statut);

        $this->actingAs($sender)
            ->post(route('transactions.cancellation-requests.store', $transaction), [
            'reference' => $transaction->reference,
            'amount' => 9999,
            'phone' => $sender->telephone,
            'reason' => 'Je ne reconnais pas cette opération.',
        ])->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('transaction_cancellation_requests', 0);

        $this->post(route('transactions.cancellation-requests.store', $transaction), [
            'reference' => $transaction->reference,
            'amount' => $transaction->montant,
            'phone' => $sender->telephone,
            'reason' => 'Je ne reconnais pas cette opération.',
        ])->assertSessionHasNoErrors();
        $cancellationRequest = \App\Models\TransactionCancellationRequest::firstOrFail();

        $this->actingAs($agent)
            ->get(route('agent.cancellation-requests.index'))
            ->assertOk()
            ->assertSee($transaction->reference)
            ->assertSee('J’ai vérifié les informations');

        $this->post(route('agent.cancellation-requests.approve', $cancellationRequest), [
                'confirmed_facts' => '1',
                'review_note' => 'Référence, montant et téléphone vérifiés.',
            ])
            ->assertRedirect();

        $this->assertSame('annulee', $transaction->fresh()->statut);
        $this->assertSame('11000.00', $senderAccount->fresh()->solde);
        $this->assertSame('0.00', $recipientAccount->fresh()->solde);
        $this->assertDatabaseHas('transaction_cancellation_requests', [
            'id' => $cancellationRequest->id,
            'status' => 'approved',
            'reviewed_by_user_id' => $agent->id,
        ]);
    }

    public function test_client_can_cancel_its_own_transaction_during_the_fifteen_minute_window(): void
    {
        $sender = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::CLIENT);
        $senderAccount = $sender->comptes()->firstOrFail();
        $recipientAccount = $recipient->comptes()->firstOrFail();
        $senderAccount->update(['solde' => 50]);
        $recipientAccount->update(['solde' => 1000]);
        $this->travelTo(now()->startOfSecond());
        $transaction = Transaction::create([
            'reference' => 'PAY-CLIENT-CANCEL-001',
            'actor_id' => $sender->id,
            'compte_source_id' => $senderAccount->id,
            'compte_destination_id' => $recipientAccount->id,
            'type' => 'paiement_qr',
            'montant' => 500,
            'frais' => 10,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'traitee_at' => now()->subMinutes(15),
        ]);
        $transaction->created_at = now()->subMinutes(15);
        $transaction->save();

        $this->actingAs($sender)
            ->post(route('client.transactions.cancel', $transaction))
            ->assertRedirect();

        $this->assertSame('550.00', $senderAccount->fresh()->solde);
        $this->assertSame('510.00', $recipientAccount->fresh()->solde);
        $this->assertSame('annulee', $transaction->fresh()->statut);
    }

    private function createUser(string $role): Users2
    {
        return Users2::create([
            'nom' => 'Test',
            'prenom' => 'Wallet',
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
