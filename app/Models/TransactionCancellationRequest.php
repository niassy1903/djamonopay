<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionCancellationRequest extends Model
{
    protected $fillable = [
        'transaction_id',
        'requester_id',
        'verified_reference',
        'verified_amount',
        'verified_phone',
        'reason',
        'status',
        'reviewed_by_user_id',
        'review_note',
        'reviewed_at',
    ];

    protected $casts = [
        'verified_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Users2::class, 'requester_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Users2::class, 'reviewed_by_user_id');
    }
}
