<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Transaction;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchGlobalTopSponsors
{
    const LAST_WEEK = 1;

    const LAST_MONTH = 2;

    public function resolve($rootValue, array $args)
    {
        $period = null;

        if (isset($args['period'])) {
            if ($args['period'] === self::LAST_WEEK) {
                $period = [
                    \Carbon\Carbon::today()->subWeek(),
                    \Carbon\Carbon::today()
                ];
            } elseif ($args['period'] === self::LAST_MONTH) {
                $period = [
                    \Carbon\Carbon::today()->subMonth(),
                    \Carbon\Carbon::today()
                ];
            }
        }

        $sponsors = User::whereHas('creditTransactions', function ($query) use ($period) {
            $query->where('type', Transaction::DONATION_TYPE)
                ->where('status', Transaction::STATUS_ACCEPTED);
            if ($period) {
                $query->whereBetween('updated_at', $period);
            }
        })->get();

        $sponsors->map(function ($user) use ($period) {
            $donations = $user->creditTransactions()
                ->where('type', Transaction::DONATION_TYPE)
                ->where('status', Transaction::STATUS_ACCEPTED);

            if ($period) {
                $donations->whereBetween('updated_at', $period);
            }

            $user->donationsSum = $donations->get()
                      ->sum('amount');
        });

        return $sponsors->sortByDesc('donationsSum')
                        ->take($args['amount'] ?? null);
    }
}
