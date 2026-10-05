<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'idempotency_key',
        'actor_id',
        'processed_by_user_id',
        'cancelled_by_user_id',
        'parent_transaction_id',
        'compte_source_id',
        'compte_destination_id',
        'type',
        'montant',
        'frais',
        'devise',
        'statut',
        'description',
        'traitee_at',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'frais' => 'decimal:2',
        'traitee_at' => 'datetime',
    ];

    public function compteSource(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_source_id');
    }

    public function compteDestination(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_destination_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Users2::class, 'actor_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(Users2::class, 'cancelled_by_user_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Users2::class, 'processed_by_user_id');
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_transaction_id');
    }

    public function cancellationRequests(): HasMany
    {
        return $this->hasMany(TransactionCancellationRequest::class);
    }
}
