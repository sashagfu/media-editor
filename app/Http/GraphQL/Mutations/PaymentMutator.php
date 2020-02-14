<?php

namespace App\Http\GraphQL\Mutations;

use App\Jobs\ExpirePayment;
use App\Models\AutoOperation;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AcceptedDonationNotification;
use App\Notifications\DeclinedDonationNotification;
use App\Notifications\NewDonationNotification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use PayPal\Api\Amount;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction as PaypalTransaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaymentMutator
{
    const DONATION_TYPE = 'donation';
    const VERIFY_TYPE = 'verify';
    const TOP_UP_TYPE = 'top-up';

    public function __construct()
    {
        $paypal_conf = \Config::get('paypal');

        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function createInternalDonation($root, $args)
    {
        // Pass reCaptch
        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params'=>
                 [
                     'secret' => env('RECAPTCHA_SECRET_KEY'),
                     'response' => $args['verify']
                 ],
            ]
        );
        $body = json_decode((string)$response->getBody());

        if (! $body->success) {
            return response()->json(
                [
                    'status'     => 'fail',
                    'message' => 'Validation failed',
                    'statusCode' => '500',
                ],
                200
            )->getData();
        }

        $payer = Auth::user();
        $payee = User::findOrFail($args['payeeId']);

        if ($payer->balance < $args['amount']) {
            return response()->json([
                'status' => 'fail',
            ], 200)->getData();
        }

        $transaction = new Transaction();
        $transaction->type = Transaction::DONATION_TYPE;
        $transaction->payer_id = $payer->id;
        $transaction->payee_id = $payee->id;
        $auto_operation = AutoOperation::where('initiator_id', $payer->id)
                                              ->where('subject_id', $payee->id)
                                              ->first();
        if ($auto_operation) {
            if ($auto_operation->status === AutoOperation::STATUS_ALWAYS_DECLINE) {
                $transaction->status = Transaction::STATUS_DECLINED;
            } else {
                $transaction->status = Transaction::STATUS_ACCEPTED;
            }
        } else {
            $transaction->status = Transaction::STATUS_PENDING;
        }
        $transaction->amount = $args['amount'];
        $transaction->save();

        \Cache::tags(['users', 'transactions'])->flush();

        if (!$auto_operation) {
            // Broadcast new donation
            Subscription::broadcast('donationCreated', $transaction);
        }

        // Update user balance
        Subscription::broadcast('userUpdated', $payer);
        Subscription::broadcast('userUpdated', $payee);

        $transaction->payee->notify(new NewDonationNotification($transaction));

        $expireJob = dispatch(new ExpirePayment($transaction))
            ->delay(now()->addDays(3));

        return response()->json([
            'status' => 'success',
            'statusCode' => '200',
        ], 200)->getData();
    }

    public function acceptDonation($root, $args)
    {
        $payee = Auth::user();

        $transaction = Transaction::find($args['id']);

        if ($payee->id !== $transaction->payee_id) {
            return response()->json([
                'status' => 'fail',
                'statusCode' => 401
            ], 200)->getData();
        }

        // if this is internal donation
        if (!isset($transaction->transaction_data['authorizationId'])) {
            // Accept the donation
            $transaction->status = Transaction::STATUS_ACCEPTED;
            $transaction->save();

            // Clear cache
            \Cache::tags(['users', 'transactions'])->flush();

            // Broadcast update balance
            Subscription::broadcast('userUpdated', $transaction->payee);
            Subscription::broadcast('userUpdated', $transaction->payer);

            // Notify payer about accepted donation
            $transaction->payer->notify(new AcceptedDonationNotification($transaction));

            return response()->json([
                'status' => 'success',
                'statusCode' => 200
            ], 200)->getData();
        }

        return $this->capturePaypalAuthorization($transaction);
    }

    private function capturePaypalAuthorization($transaction)
    {
        $authorization = Authorization::get($transaction->transaction_data['authorizationId'], $this->_api_context);

        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($transaction->amount);

        $capture = new Capture();
        $capture->setAmount($amount);

        try {
            $result = $authorization->capture($capture, $this->_api_context);
        } catch (\Exception $e) {
            $transaction->status = Transaction::STATUS_FAILED;
            $transaction->save();

            return response()->json([
                'status' => 'fail',
                'statusCode' => 500
            ], 200)->getData();
        }

        if ($result->getState() == 'completed') {
            $transaction->status = Transaction::STATUS_ACCEPTED;
            $transaction->save();
            \Cache::tags(['transactions'])->flush();

            // Broadcast update balance
            Subscription::broadcast('userUpdated', $transaction->payee);
            Subscription::broadcast('userUpdated', $transaction->payer);

            // Send notification
            $transaction->payer->notify(new AcceptedDonationNotification($transaction));

            return response()->json([
                'status' => 'success',
                'statusCode' => 200
            ], 200)->getData();
        }

        return response()->json([
            'status' => 'fail',
            'statusCode' => 500
        ], 200)->getData();
    }

    public function declineDonation($root, $args)
    {
        $payee = Auth::user();

        $transaction = Transaction::find($args['id']);

        if ($payee->id !== $transaction->payee_id) {
            return response()->json([
                'status' => 'fail',
                'statusCode' => 401
            ], 200)->getData();
        }

        // Accept the donation
        $transaction->status = Transaction::STATUS_DECLINED;
        $transaction->save();

        // Clear cache
        \Cache::tags(['users', 'transactions'])->flush();

        // Notify payer about declined donation
        $transaction->payer->notify(new DeclinedDonationNotification($transaction));

        return response()->json([
            'status' => 'success',
            'statusCode' => 200
        ], 200)->getData();
    }

    public function createTopUp($root, $args)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $donation = new Item();

        $user = Auth::user();

        $donation->setName("Top up Actionlime internal balance")
                 ->setCurrency('USD')
                 ->setQuantity(1)
                 ->setPrice($args['amount'])
                 ->setUrl("http://actionlime.test/profile/{$user->username}");

        $item_list = new ItemList();
        $item_list->setItems(array($donation));

        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($args['amount']);

        $paypalTransaction = new PaypalTransaction();
        $paypalTransaction->setAmount($amount)
                          ->setItemList($item_list)
                          ->setDescription('Top-up Actionlime internal balance');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('ipn'))
                      ->setCancelUrl(URL::route('front.welcome'));

        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($paypalTransaction));

        try {
            $payment->create($this->_api_context);
        } catch (PayPalConnectionException $e) {
            $this->sendFailResponse(
                $this->redirect_url,
                null,
                self::TOP_UP_TYPE
            );
        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        // Store data in the database
        $transaction = new Transaction();
        $transaction->payer_id = $user->id;
        $transaction->payee_id = $user->id;
        $transaction->amount = $args['amount'];
        $transaction->type = Transaction::TOP_UP_TYPE;
        $transaction->transaction_data = [
            'paymentId' => $payment->id,
        ];

        $transaction->save();

        return response()->json([
            'status' => 'success',
            'statusCode' => 200,
            'redirectUrl' => $redirect_url
        ], 200)->getData();
    }

    public function toggleAutoAcceptDonation($root, $args)
    {
        $user = Auth::user();
        $operation = AutoOperation::where('subject_id', $user->id)
                                  ->where('initiator_id', $args['userId'])
                                  ->where('type', Transaction::DONATION_TYPE)
                                  ->where('status', AutoOperation::STATUS_ALWAYS_ACCEPT)
                                  ->first();

        if ($operation) {
            return $this->deleteAutoOperation($operation);
        }

        return $this->createAutoOperation($args['userId'], AutoOperation::STATUS_ALWAYS_ACCEPT);
    }

    public function toggleAutoDeclineDonation($root, $args)
    {
        $user = Auth::user();
        $operation = AutoOperation::where('subject_id', $user->id)
                                  ->where('initiator_id', $args['userId'])
                                  ->where('type', Transaction::DONATION_TYPE)
                                  ->where('status', AutoOperation::STATUS_ALWAYS_DECLINE)
                                  ->first();
        if ($operation) {
            return $this->deleteAutoOperation($operation);
        }

        return $this->createAutoOperation($args['userId'], AutoOperation::STATUS_ALWAYS_DECLINE);
    }

    private function createAutoOperation($initiator_id, $status)
    {
        $operation = new AutoOperation();
        $operation->initiator_id = $initiator_id;
        $operation->subject_id = Auth::id();
        $operation->type = Transaction::DONATION_TYPE;
        $operation->status = $status;
        $operation->save();

        return response()->json([
            'status' => 'success',
            'statusCode' => 200
        ], 200)->getData();
    }

    private function deleteAutoOperation(AutoOperation $operation)
    {
        $operation->delete();

        return response()->json([
            'status' => 'success',
            'statusCode' => 200
        ], 200)->getData();
    }

    public function acceptAllDonations($root, $args)
    {
        $user = Auth::user();

        $donations = $user->pendingDonations;

        try {
            foreach ($donations as $donation) {
                $this->acceptDonation(null, ['id' => $donation->id]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'statusCode' => 500
            ])->getData();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'All donations are accepted successfully',
            'statusCode' => 200
        ])->getData();
    }

    public function declineAllDOnations($root, $args)
    {
        $user = Auth::user();

        $donations = $user->pendingDonations;

        try {
            foreach ($donations as $donation) {
                $this->declineDonation(null, ['id' => $donation->id]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'statusCode' => 500
            ])->getData();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'All donations are declined successfully',
            'statusCode' => 200
        ])->getData();
    }
}
