<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compte extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_compte',
        'solde',
        'devise',
        'statut',
    ];

    protected $casts = [
        'solde' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users2::class, 'user_id');
    }

    public function transactionsSortantes(): HasMany
    {
        return $this->hasMany(Transaction::class, 'compte_source_id');
    }

    public function transactionsEntrantes(): HasMany
    {
        return $this->hasMany(Transaction::class, 'compte_destination_id');
    }
}
