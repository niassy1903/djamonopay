<?php

namespace Database\Seeders;

use App\Models\Users2;
use Illuminate\Database\Seeder;
use LogicException;

abstract class DemoRoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('Demo users can only be seeded in local or testing environments.');
        }

        $password = env('DEMO_SEED_PASSWORD', 'password');

        for ($index = 1; $index <= $this->accountCount(); $index++) {
            $suffix = str_pad((string) $index, 3, '0', STR_PAD_LEFT);

            $user = Users2::firstOrCreate(
                ['email' => $this->accountEmail($index, $suffix)],
                [
                    'nom' => fake()->lastName(),
                    'prenom' => fake()->firstName(),
                    'mot_de_passe' => $password,
                    'role' => $this->role(),
                    'telephone' => fake()->numerify('77#######'),
                    'adresse' => fake()->city().', Sénégal',
                    'date_naissance' => fake()->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
                    'numero_identite' => 'DEMO-'.strtoupper($this->prefix()).'-'.$suffix,
                    'etat_compte' => true,
                ]
            );

            $user->comptes()->firstOrCreate(
                [],
                [
                    'numero_compte' => 'DP'.str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
                    'solde' => 0,
                    'devise' => 'XOF',
                    'statut' => 'actif',
                ]
            );
        }
    }

    abstract protected function role(): string;

    abstract protected function prefix(): string;

    abstract protected function accountCount(): int;

    protected function accountEmail(int $index, string $suffix): string
    {
        return $this->prefix().".{$suffix}@djamonopay.test";
    }
}
