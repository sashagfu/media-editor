<?php

namespace App\Http\GraphQL\Types;

use App\Models\Transaction;

class TransactionType
{
    public function payerId(Transaction $transaction)
    {
        return $transaction->payer_id;
    }

    public function payeeId(Transaction $transaction)
    {
        return $transaction->payee_id;
    }

    public function createdAt(Transaction $transaction)
    {
        return $transaction->created_at;
    }

    public function updatedAt(Transaction $transaction)
    {
        return $transaction->updated_at;
    }
}
