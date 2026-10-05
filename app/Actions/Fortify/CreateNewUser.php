<?php

namespace App\Actions\Fortify;

use App\Enums\UserRole;
use App\Models\Users2;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): Users2
    {
        $input['email'] = Str::lower($input['email']);

        Validator::make($input, [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users2,email'],
            'telephone' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'numero_identite' => ['required', 'string', 'max:255', 'unique:users2,numero_identite'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(fn () => Users2::create([
            'nom' => $input['nom'],
            'prenom' => $input['prenom'],
            'email' => $input['email'],
            'mot_de_passe' => $input['password'],
            'role' => UserRole::CLIENT,
            'telephone' => $input['telephone'],
            'adresse' => $input['adresse'],
            'date_naissance' => $input['date_naissance'],
            'numero_identite' => $input['numero_identite'],
        ]));
    }
}
