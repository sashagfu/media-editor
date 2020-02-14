<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class FetchTransactions
{
    public function resolve($rootValue, array $args)
    {
        $user = Auth::user();

        $credit = $user->creditTransactions()
            ->whereIn('status', Transaction::SUCCESS_STATUSES);

        $debit = $user->debitTransactions()
            ->whereIn('status', Transaction::SUCCESS_STATUSES);

        if (isset($args['range'])) {
            $range = [
                $args['range']['from'],
                $args['range']['to'],
            ];

            $credit->whereBetween('updated_at', $range);
            $debit->whereBetween('updated_at', $range);
        }

        $transactions = $debit->get()->merge($credit->get());

        return $transactions->sortByDesc('updated_at');
    }
}
