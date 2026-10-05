<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users2 extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'users2';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'photo',
        'mot_de_passe',
        'role',
        'telephone',
        'adresse',
        'date_naissance',
        'numero_identite',
        'etat_compte',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'etat_compte' => 'boolean',
        'mot_de_passe' => 'hashed',
    ];

    protected static function booted(): void
    {
        static::created(function (self $user): void {
            $user->comptes()->create([
                'numero_compte' => 'DP'.str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
                'solde' => 0,
                'devise' => 'XOF',
                'statut' => 'actif',
            ]);
        });
    }

    protected $appends = [
        'name',
        'profile_photo_url',
    ];

    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function getPasswordAttribute(): string
    {
        return $this->mot_de_passe;
    }

    public function getNameAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    public function comptes(): HasMany
    {
        return $this->hasMany(Compte::class, 'user_id');
    }

    public function comptesXof(): HasMany
    {
        return $this->comptes()->where('devise', 'XOF');
    }
}
