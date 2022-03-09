<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = ['descriptions' => 'array'];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    public function failed(): HasOne
    {
        return $this->hasOne(FailedTransaction::class);
    }

    public function successful(): HasOne
    {
        return $this->hasOne(SuccessfulTransaction::class);
    }
}
