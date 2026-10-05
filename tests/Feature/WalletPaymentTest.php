<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WalletPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_and_distributor_dashboards_show_real_wallet_data_and_payment_qr(): void
    {
        $this->withoutVite();
        $client = $this->createUser(UserRole::CLIENT);
        $client->comptes()->firstOrFail()->update(['solde' => 24000]);

        $this->actingAs($client)
            ->get(route('dashboard-client'))
            ->assertOk()
            ->assertSee('24 000')
            ->assertSee('Votre QR code')
            ->assertSee('data-payment-qr')
            ->assertSee('Aucune transaction sur les six derniers mois')
            ->assertSee(route('payments.new', ['recipient_account' => $client->comptes()->firstOrFail()->numero_compte]), false);

        $distributor = $this->createUser(UserRole::DISTRIBUTEUR);
        $this->actingAs($distributor)
            ->get(route('dashboard-distributeur'))
            ->assertOk()
            ->assertSee('Mon espace distributeur')
            ->assertSee('Vos mouvements')
            ->assertSee('Aucune transaction sur les six derniers mois');
    }

    public function test_qr_payment_moves_funds_atomically_and_records_a_transaction_and_activity(): void
    {
        $sender = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::DISTRIBUTEUR);
        $senderAccount = $sender->comptes()->firstOrFail();
        $recipientAccount = $recipient->comptes()->firstOrFail();
        $senderAccount->update(['solde' => 12500]);
        $paymentToken = (string) Str::uuid();

        $this->actingAs($sender)
            ->get(route('payments.new', ['recipient_account' => $recipientAccount->numero_compte]))
            ->assertOk()
            ->assertSee($recipient->name)
            ->assertSee('Bénéficiaire vérifié');

        $this->post(route('payments.store'), [
            'operation' => 'paiement',
            'recipient_account' => $recipientAccount->numero_compte,
            'amount' => 3500,
            'payment_token' => $paymentToken,
        ])->assertRedirect(route('payments.new', ['recipient_account' => $recipientAccount->numero_compte]))
            ->assertSessionHas('payment_amount', 3500)
            ->assertSessionHas('payment_received', 3430)
            ->assertSessionHas('payment_fee', 70);

        $this->assertSame('9000.00', $senderAccount->fresh()->solde);
        $this->assertSame('3430.00', $recipientAccount->fresh()->solde);
        $this->assertDatabaseHas('transactions', [
            'compte_source_id' => $senderAccount->id,
            'compte_destination_id' => $recipientAccount->id,
            'montant' => 3500,
            'frais' => 70,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'type' => 'paiement_qr',
            'idempotency_key' => $paymentToken,
        ]);
        $this->assertDatabaseHas('system_loggers', [
            'user_id' => $sender->id,
            'action' => 'wallet.payment.completed',
        ]);

        $this->assertSame(1, Transaction::count());

        $this->post(route('payments.store'), [
            'operation' => 'paiement',
            'recipient_account' => $recipientAccount->numero_compte,
            'amount' => 3500,
            'payment_token' => $paymentToken,
        ])->assertRedirect(route('payments.new', ['recipient_account' => $recipientAccount->numero_compte]));

        $this->assertSame('9000.00', $senderAccount->fresh()->solde);
        $this->assertSame('3430.00', $recipientAccount->fresh()->solde);
        $this->assertSame(1, Transaction::count());

        $this->actingAs($recipient)
            ->get(route('dashboard-distributeur'))
            ->assertOk()
            ->assertSee('3 430 XOF');
    }

    public function test_payment_can_resolve_a_beneficiary_by_phone_and_show_their_name(): void
    {
        $sender = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::CLIENT);
        $senderAccount = $sender->comptes()->firstOrFail();
        $recipientAccount = $recipient->comptes()->firstOrFail();
        $senderAccount->update(['solde' => 12500]);
        $recipient->update(['telephone' => '+221 77-123-45-67']);
        $phone = '77 123 45 67';

        $this->actingAs($sender)
            ->getJson(route('recipients.lookup', ['phone' => $phone, 'operation' => 'paiement']))
            ->assertOk()
            ->assertJsonPath('name', $recipient->name);

        $this->get(route('payments.new', ['recipient_account' => $phone]))
            ->assertOk()
            ->assertSee($recipient->name)
            ->assertSee('Bénéficiaire vérifié');

        $this->post(route('payments.store'), [
            'operation' => 'paiement',
            'recipient_account' => $phone,
            'amount' => 3500,
            'payment_token' => (string) Str::uuid(),
        ])->assertRedirect(route('payments.new', ['recipient_account' => $recipientAccount->numero_compte]));

        $this->assertSame('9000.00', $senderAccount->fresh()->solde);
        $this->assertSame('3430.00', $recipientAccount->fresh()->solde);
    }

    public function test_phone_lookup_rejects_ambiguous_numbers_and_ineligible_roles(): void
    {
        $client = $this->createUser(UserRole::CLIENT);
        $firstRecipient = $this->createUser(UserRole::CLIENT);
        $secondRecipient = $this->createUser(UserRole::CLIENT);
        $phone = '771234567';
        $firstRecipient->update(['telephone' => $phone]);
        $secondRecipient->update(['telephone' => '+221'.$phone]);

        $this->actingAs($client)
            ->getJson(route('recipients.lookup', ['phone' => $phone, 'operation' => 'paiement']))
            ->assertNotFound();

        $this->getJson(route('recipients.lookup', [
            'phone' => $firstRecipient->telephone,
            'operation' => 'retrait',
        ]))->assertNotFound();
    }

    public function test_payment_rejects_insufficient_funds_without_changing_either_account(): void
    {
        $sender = $this->createUser(UserRole::CLIENT);
        $recipient = $this->createUser(UserRole::CLIENT);
        $recipientAccount = $recipient->comptes()->firstOrFail();

        $this->actingAs($sender)
            ->from(route('payments.new'))
            ->post(route('payments.store'), [
                'operation' => 'paiement',
                'recipient_account' => $recipientAccount->numero_compte,
                'amount' => 1,
                'payment_token' => (string) Str::uuid(),
            ])
            ->assertSessionHasErrors('amount');

        $this->assertSame('0.00', $sender->comptes()->firstOrFail()->fresh()->solde);
        $this->assertSame('0.00', $recipientAccount->fresh()->solde);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('system_loggers', 0);
    }

    public function test_only_clients_and_distributors_can_make_wallet_payments(): void
    {
        $agent = $this->createUser(UserRole::AGENT);

        $this->actingAs($agent)
            ->get(route('payments.new'))
            ->assertForbidden();
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
