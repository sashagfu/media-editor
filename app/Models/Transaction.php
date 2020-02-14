<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends BaseModel
{

    const DONATION_TYPE = 'donation';

    const VERIFICATION_TYPE = 'verification';

    const WITH_DRAW_TYPE = 'withdraw';

    const TOP_UP_TYPE = 'top-up';

    const DEBIT = [self::DONATION_TYPE, self::TOP_UP_TYPE];

    const CREDIT = [self::WITH_DRAW_TYPE];

    const STATUS_SUCCESS = 1;

    const STATUS_PENDING = 2;

    const STATUS_DECLINED = 3;

    const STATUS_ACCEPTED = 4;

    const STATUS_FAILED = 5;

    const STATUS_REFUNDED = 6;

    const STATUS_EXPIRED = 7;

    const SUCCESS_STATUSES = [self::STATUS_SUCCESS, self::STATUS_ACCEPTED, self::STATUS_PENDING];

    protected $casts = [
        'transaction_data' => 'json'
    ];

    public function payer()
    {
        return $this->belongsTo(User::class);
    }

    public function payee()
    {
        return $this->belongsTo(User::class);
    }
}
