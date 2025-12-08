<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id_to',
        'wallet_id',
        'amount',
        'type',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
