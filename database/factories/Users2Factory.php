<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Users2;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Users2>
 */
class Users2Factory extends Factory
{
    protected $model = Users2::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'mot_de_passe' => 'password',
            'role' => UserRole::CLIENT,
            'telephone' => fake()->numerify('##########'),
            'adresse' => fake()->address(),
            'date_naissance' => '1990-01-01',
            'numero_identite' => fake()->unique()->numerify('##########'),
            'etat_compte' => true,
        ];
    }
}
