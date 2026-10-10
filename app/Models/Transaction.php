<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $guarded = [];

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Nomor berikutnya, format TRX-YYYYMMDD-001 (urut per hari).
     */
    public static function generateNumber(): string
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';

        $last = static::where('transaction_number', 'like', $prefix . '%')
            ->max('transaction_number');

        $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
