<?php

namespace App\Http\GraphQL\Queries;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FetchOutcomingDonationsHistory
{
    public function resolve($rootValue, array $args)
    {
        $user = Auth::user();

        return User::whereHas('debitTransactions', function ($query) use ($user) {
            $query->where('payer_id', $user->id);
        })
                   ->get();
    }
}
