<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FetchIncomingDonationsHistory
{
    public function resolve($rootValue, array $args)
    {
        $user = Auth::user();

        return User::whereHas('creditTransactions', function ($query) use ($user) {
            $query->where('payee_id', $user->id)
            ->where('type', Transaction::DONATION_TYPE);
        })
            ->get();
    }
}
