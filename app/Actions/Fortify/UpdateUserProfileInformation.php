<?php

namespace App\Actions\Fortify;

use App\Models\Users2;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(Users2 $user, array $input): void
    {
        $nameParts = array_pad(preg_split('/\s+/', trim($input['name'] ?? $user->name), 2), 2, '');
        $input['prenom'] = $input['prenom'] ?? $nameParts[0];
        $input['nom'] = $input['nom'] ?? $nameParts[1] ?: $user->nom;
        $input['telephone'] = $input['telephone'] ?? $user->telephone;
        $input['adresse'] = $input['adresse'] ?? $user->adresse;
        $input['date_naissance'] = $input['date_naissance'] ?? $user->date_naissance?->toDateString();
        $input['numero_identite'] = $input['numero_identite'] ?? $user->numero_identite;
        $input['email'] = Str::lower($input['email']);

        Validator::make($input, [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users2', 'email')->ignore($user->id)],
            'telephone' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'numero_identite' => ['required', 'string', 'max:255', Rule::unique('users2', 'numero_identite')->ignore($user->id)],
        ])->validateWithBag('updateProfileInformation');

        $user->forceFill([
            'prenom' => $input['prenom'],
            'nom' => $input['nom'],
            'email' => $input['email'],
            'telephone' => $input['telephone'],
            'adresse' => $input['adresse'],
            'date_naissance' => $input['date_naissance'],
            'numero_identite' => $input['numero_identite'],
        ])->save();
    }
}
