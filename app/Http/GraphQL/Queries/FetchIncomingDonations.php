<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Transaction;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchIncomingDonations
{
    public function resolve($rootValue, array $args)
    {
        $query = Transaction::query();

        if ($args['userId']) {
            $query->where('payee_id', $args['userId'])
                ->where('type', Transaction::DONATION_TYPE)
                ->whereNotIn('status', [Transaction::STATUS_FAILED, Transaction::STATUS_REFUNDED]);
        }

        if (isset($args['status'])) {
            $query->where('status', $args['status']);
        }

        return $query->get();
    }
}
