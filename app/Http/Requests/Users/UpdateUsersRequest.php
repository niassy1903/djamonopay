<?php

namespace App\Http\Requests\Users;

use App\Enums\UserRole;
use App\Models\Users2;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateUsersRequest extends FormRequest
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
        $target = $this->route('users');
        $targetId = $target instanceof Users2 ? $target->id : null;

        return [
            'nom' => ['sometimes', 'string', 'max:255'],
            'prenom' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users2', 'email')->ignore($targetId)],
            'photo' => ['nullable', 'string', 'max:255'],
            'mot_de_passe' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'string', Rule::in([UserRole::CLIENT, UserRole::DISTRIBUTEUR, UserRole::AGENT])],
            'telephone' => ['sometimes', 'string', 'max:255'],
            'adresse' => ['sometimes', 'string', 'max:255'],
            'date_naissance' => ['sometimes', 'date', 'before:today'],
            'numero_identite' => ['sometimes', 'string', 'max:255', Rule::unique('users2', 'numero_identite')->ignore($targetId)],
            'etat_compte' => ['sometimes', 'boolean'],
        ];
    }
}
