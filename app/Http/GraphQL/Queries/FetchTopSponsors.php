<?php

namespace App\Http\GraphQL\Queries;

use App\Models\User;

class FetchTopSponsors
{
    const LAST_WEEK = 1;

    const LAST_MONTH = 2;

    public function resolve($rootValue, array $args)
    {
        $user = User::find($args['userId']);

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

        // Sort sponsors by donations sum
        $sponsors = $user->sponsors->sortByDesc(function ($sponsor, $key) use ($user, $period) {
            return $sponsor->getDonationsSum($user->id, $period ?? null);
        });

        // Filter sponsors
        $sponsors = $sponsors->filter(function ($sponsor, $value) use ($user, $period) {
            return $sponsor->getDonationsSum($user->id, $period ?? null) > 0;
        });

        $sponsors->map(function ($sponsor) use ($user) {
            $sponsor->donationsSum = $sponsor->getDonationsSum($user->id, $period ?? null);
        });

        return $sponsors->take($args['amount'] ?? null);
    }
}
