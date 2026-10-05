<?php

namespace App\Http\Requests\Users;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::AGENT;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($email = $this->input('email'))) {
            $this->merge(['email' => Str::lower($email)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users2,email'],
            'photo' => ['nullable', 'string', 'max:255'],
            'mot_de_passe' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in([UserRole::CLIENT, UserRole::DISTRIBUTEUR, UserRole::AGENT])],
            'telephone' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'numero_identite' => ['required', 'string', 'max:255', 'unique:users2,numero_identite'],
            'etat_compte' => ['sometimes', 'boolean'],
        ];
    }
}
