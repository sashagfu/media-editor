<?php

namespace App\Http\GraphQL\Types;

use App\Models\Transaction;
use App\Models\User;

class UserType
{
    public function displayName(User $user)
    {
        return $user->display_name;
    }

    public function createdAt(User $user)
    {
        return $user->created_at;
    }

    public function updatedAt(User $user)
    {
        return $user->updated_at;
    }

    public function incomingDonations(User $user, $args)
    {
        $query = Transaction::query();

        if (isset($args['payerId'])) {
            $query->where('payer_id', $args['payerId']);
        }

        $query->where('payee_id', $user->id)
              ->where('type', Transaction::DONATION_TYPE)
              ->whereNotIn('status', [Transaction::STATUS_FAILED, Transaction::STATUS_REFUNDED]);

        return $query->get();
    }

    public function outcomingDonations(User $user, $args)
    {
        $query = Transaction::query();

        if (isset($args['payeeId'])) {
            $query->where('payee_id', $args['payeeId']);
        }

        $query->where('payer_id', $user->id)
              ->where('type', Transaction::DONATION_TYPE)
              ->whereNotIn('status', [Transaction::STATUS_FAILED, Transaction::STATUS_REFUNDED]);

        return $query->get();
    }
}
