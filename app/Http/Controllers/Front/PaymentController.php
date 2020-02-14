<?php

namespace App\Http\Controllers\Front;

use App\Jobs\ExpirePayment;
use App\Models\AutoOperation;
use App\Models\User;
use App\Notifications\AcceptedDonationNotification;
use App\Notifications\NewDonationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use PayPal\Api\Amount;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;
use PayPal\Api\Currency;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payee;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Payout;
use PayPal\Api\PayoutBatchHeader;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\RedirectUrls;
use PayPal\Api\RefundRequest;
use PayPal\Api\Transaction as PaypalTransaction;
use App\Models\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaymentController extends Controller
{
    const VERIFICATION_AMOUNT = 1;

    const PAYOUTS_ITEM_RESOURCE_TYPE = 'payouts_item';
    const PAYOUTS_ITEM_SUCCESS_STATUS = 'SUCCESS';
    const PAYOUTS_ITEM_FAILED_STATUS = 'FAILED';

    const DONATION_TYPE = 'donation';
    const VERIFY_TYPE = 'verify';
    const TOP_UP_TYPE = 'top-up';

    protected $redirect_url = '/profile';

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

    public function createDonation(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $donation = new Item();

        $payee = User::find($request->payeeId);

        $donation->setName("Donation for {$payee->display_name} on actionlime")
                 ->setCurrency('USD')
                 ->setQuantity(1)
                 ->setPrice($request->amount)
                 ->setUrl("http://actionlime.test/profile/{$payee->username}");

        $item_list = new ItemList();
        $item_list->setItems(array($donation));

        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal($request->amount);

        $paypalTransaction = new PaypalTransaction();
        $paypalTransaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('This is my description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('ipn'))
                      ->setCancelUrl(URL::route('front.welcome'));

        $payment = new Payment();
        $payment->setIntent('authorize')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($paypalTransaction));

        try {
            $payment->create($this->_api_context);
        } catch (PayPalConnectionException $e) {
            $this->sendFailResponse(
                $this->redirect_url,
                null,
                self::DONATION_TYPE
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
        $transaction->payer_id = Auth::id();
        $transaction->payee_id = $payee->id;
        $transaction->amount = $request->amount;
        $transaction->type = Transaction::DONATION_TYPE;
        $transaction->transaction_data = [
            'paymentId' => $payment->id,
        ];

        $transaction->save();

        return $redirect_url;
    }

    public function ipn(Request $request)
    {
        $payment_id = $request->paymentId;

        $transaction = Transaction::where('transaction_data->paymentId', $payment_id)->first();

        return ($transaction->type === Transaction::DONATION_TYPE)
            ? $this->createAuthorization($request, $transaction)
            : $this->captureTopUp($request, $transaction);
    }

    private function createAuthorization(Request $request, Transaction $transaction)
    {
        /** Get the payment ID before session clear **/
        $payment_id = $request->paymentId;

        $payer_id = $request->PayerID;
        $token = $request->token;

        $payee = $transaction->payee;

        $redirect_url = $this->redirect_url . '/' . $payee->username;

        /** clear the session payment ID **/
        if (!$payer_id || !$token) {
            return $this->sendFailResponse(
                $redirect_url,
                $transaction,
                self::DONATION_TYPE
            );
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($payer_id);

        /**Execute the payment **/
        try {
            $result = $payment->execute($execution, $this->_api_context);

            $authorization_id = $result->transactions[0]
                                    ->related_resources[0]
                                        ->authorization->id;

            $authorization = Authorization::get($authorization_id, $this->_api_context);

            $payer = Auth::user();

            $transaction_data = $transaction->transaction_data;

            $transaction_data = array_merge($transaction_data, [
                'token' => $token,
                'payerId' => $payer_id,
                'authorizationId' => $authorization->id,
            ]);

            $transaction->transaction_data = $transaction_data;
            $transaction->save();
        } catch (\Exception $e) {
            $this->sendFailResponse(
                $redirect_url,
                $transaction,
                self::DONATION_TYPE
            );
        }

        if ($result->getState() == 'approved') {
            // Make auto accept
            $auto_operation = AutoOperation::where('initiator_id', $payer->id)
                                           ->where('subject_id', $payee->id)
                                           ->first();
            if ($auto_operation) {
                if ($auto_operation->status === AutoOperation::STATUS_ALWAYS_DECLINE) {
                    $transaction->status = Transaction::STATUS_DECLINED;
                } else {
                    return $this->captureAuthorization($transaction);
                }
            } else {
                $transaction->status = Transaction::STATUS_PENDING;
            }
            $transaction->save();

            $transaction->payee->notify(new NewDonationNotification($transaction));

            $expireJob = dispatch(new ExpirePayment($transaction))
                ->delay(now()->addDays(3));

            return redirect($redirect_url . '#payment_status_ok');
        }

        return $this->sendFailResponse(
            $redirect_url,
            $transaction,
            self::DONATION_TYPE
        );
    }

    private function captureAuthorization($transaction)
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
            return $this->sendFailResponse(
                $this->redirect_url,
                $transaction,
                self::DONATION_TYPE
            );
        }

        if ($result->getState() == 'completed') {
            $transaction->status = Transaction::STATUS_ACCEPTED;
            $transaction->save();
            \Cache::tags(['transactions'])->flush();
            return redirect($this->redirect_url . '#payment_status_ok');
        }

        return $this->sendFailResponse(
            $this->redirect_url,
            $transaction,
            self::DONATION_TYPE
        );
    }

    public function createVerification(Request $request)
    {
        // Update user data
        $user = Auth::user();
        $user->setPropertyValue('user.paypal_email', $request->paypalEmail);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $verification = new Item();

        $verification->setName("Verification paypal account on Actionlime")
                 ->setCurrency('USD')
                 ->setQuantity(1)
                 ->setPrice(self::VERIFICATION_AMOUNT)
                 ->setUrl("http://actionlime.test/profile");

        $item_list = new ItemList();
        $item_list->setItems(array($verification));

        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal(self::VERIFICATION_AMOUNT);

        $paypalTransaction = new PaypalTransaction();
        $paypalTransaction->setAmount($amount)
                          ->setItemList($item_list)
                          ->setDescription('Verification paypal account on Actionlime');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('paypal.authorize'))
                      ->setCancelUrl(URL::route('front.welcome'));

        $payment = new Payment();
        $payment->setIntent('authorize')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($paypalTransaction));

        try {
            $payment->create($this->_api_context);
        } catch (PayPalConnectionException $e) {
            $this->sendFailResponse(
                $this->redirect_url,
                null,
                self::VERIFY_TYPE
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
        $transaction->payer_id = 1;
        $transaction->payee_id = Auth::id();
        $transaction->amount = self::VERIFICATION_AMOUNT;
        $transaction->type = Transaction::VERIFICATION_TYPE;
        $transaction->transaction_data = [
            'paymentId' => $payment->id,
        ];

        $transaction->save();

        return $redirect_url;
    }

    public function createVerifyAuthorization(Request $request)
    {
        $payment_id = $request->paymentId;

        $payer_id = $request->PayerID;
        $token = $request->token;

        $transaction = Transaction::where('transaction_data->paymentId', $payment_id)->first();

        if (Auth::id() !== $transaction->payer->id) {
            return $this->sendFailResponse(
                $this->redirect_url,
                $transaction,
                self::VERIFY_TYPE
            );
        }

        /** clear the session payment ID **/
        if (!$payer_id || !$token) {
            return $this->sendFailResponse(
                $this->redirect_url,
                $transaction,
                self::VERIFY_TYPE
            );
        }

        $payment = Payment::get($payment_id, $this->_api_context);

        if ($payment->payer->payer_info->email !== $transaction->payer->paypalEmail) {
            return $this->sendFailResponse(
                $this->redirect_url,
                $transaction,
                self::VERIFY_TYPE
            );
        }

        $execution = new PaymentExecution();
        $execution->setPayerId($payer_id);

        /**Execute the payment **/
        try {
            $result = $payment->execute($execution, $this->_api_context);

            $authorization_id = $result->transactions[0]
                                    ->related_resources[0]
                ->authorization->id;

            $authorization = Authorization::get($authorization_id, $this->_api_context);

            $transaction_data = $transaction->transaction_data;

            $transaction_data = array_merge($transaction_data, [
                'token' => $token,
                'payerId' => $payer_id,
                'authorizationId' => $authorization->id,
            ]);

            $transaction->transaction_data = $transaction_data;
            $transaction->save();
        } catch (\Exception $e) {
            return $this->sendFailResponse(
                $this->redirect_url,
                $transaction,
                self::VERIFY_TYPE
            );
        }

        if ($result->getState() == 'approved') {
            return $this->captureVerifyAuthorization($authorization, $transaction);
        }

        return $this->sendFailResponse(
            $this->redirect_url,
            $transaction,
            self::VERIFY_TYPE
        );
    }

    public function captureVerifyAuthorization(Authorization $authorization, $transaction)
    {
        $amount = new Amount();
        $amount->setCurrency('USD')
               ->setTotal(self::VERIFICATION_AMOUNT);

        $capture = new Capture();
        $capture->setAmount($amount);

        try {
            $capture = $authorization->capture($capture, $this->_api_context);
        } catch (\Exception $e) {
            return $this->sendFailResponse(
                $this->redirect_url,
                $transaction,
                self::VERIFY_TYPE
            );
        }

        if ($capture->getState() == 'completed') {
            $transaction_data = $transaction->transaction_data;

            $transaction_data = array_merge($transaction_data, [
                'captureId' => $capture->id,
            ]);

            $transaction->transaction_data = $transaction_data;
            $transaction->save();
            return $this->refundVerifyAuthorization($capture, $transaction);
        }

        return $this->sendFailResponse(
            $this->redirect_url,
            $transaction,
            self::VERIFY_TYPE
        );
    }

    public function refundVerifyAuthorization(Capture $capture, Transaction $transaction)
    {
        $refundRequest = new RefundRequest();

        $result = $capture->refundCapturedPayment($refundRequest, $this->_api_context);

        if ($result->getState() == 'completed') {
            $user = $transaction->payer;
            $user->setPropertyValue('user.paypal_verified', 1);
            $transaction->status = Transaction::STATUS_SUCCESS;
            $transaction->save();

            return redirect($this->redirect_url . '#verify_status_ok');
        }

        return $this->sendFailResponse(
            $this->redirect_url,
            $transaction,
            self::VERIFY_TYPE
        );
    }

    public function createPayout(Request $request)
    {
        $user = Auth::user();
        $receiver_email = $user->paypalEmail;

        if ($user->balance < $request->amount) {
            return response()->json([
                'status' => 'fail',
            ], 200);
        }

        $payout = new Payout();

        $senderBatchHeader = new PayoutSenderBatchHeader();

        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject('You have payout');

        $senderItem = new PayoutItem();

        $amount = new Currency();
        $amount->setCurrency('USD')
            ->setValue($request->amount);

        $senderItem->setRecipientType('Email')
            ->setNote('Thanks foy your patronage!')
            ->setReceiver($receiver_email)
            ->setSenderItemId(uniqid())
            ->setAmount($amount);

        $payout->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        $transaction = new Transaction();
        $transaction->type = Transaction::WITH_DRAW_TYPE;
        $transaction->payer_id = 1;
        $transaction->payee_id = Auth::id();
        $transaction->status = Transaction::STATUS_PENDING;
        $transaction->amount = $request->amount;

        try {
            $result = $payout->create(null, $this->_api_context);
            $payoutBatchId = $result->getBatchHeader()->getPayoutBatchId();
            $payoutBatch = Payout::get($payoutBatchId, $this->_api_context);
            $item = collect($payoutBatch->getItems())->first();
        } catch (\Exception $e) {
            $transaction->status = Transaction::STATUS_FAILED;
            $transaction->save();

            return response()->json([
                'status' => 'fail',
            ], 200);
        }

        $transaction->transaction_data = [
            'PayoutItemId' => $item->payout_item_id,
            'PayoutBatchId' => $item->payout_batch_id,
        ];
        $transaction->save();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function handlePaypalNotification(Request $request)
    {
        if ($request->resource_type == self::PAYOUTS_ITEM_RESOURCE_TYPE) {
            $transaction = Transaction::where('type', Transaction::WITH_DRAW_TYPE)
                                      ->where(
                                          'transaction_data->PayoutItemId',
                                          $request->resource['payout_item_id']
                                      )
                                      ->first();

            if (!$transaction) {
                return;
            }

            if ($request->resource['transaction_status'] == self::PAYOUTS_ITEM_SUCCESS_STATUS) {
                $transaction->status = Transaction::STATUS_SUCCESS;
            } else {
                $transaction->status = Transaction::STATUS_FAILED;
            }
            $transaction->save();
            \Cache::tags(['transactions'])->flush();
        }
    }

    private function captureTopUp(Request $request, $transaction)
    {
        /** Get the payment ID before session clear **/
        $payment_id = $request->paymentId;

        $payer_id = $request->PayerID;
        $token = $request->token;

        $redirect_url = $this->redirect_url;

        /** clear the session payment ID **/
        if (!$payer_id || !$token) {
            return $this->sendFailResponse(
                $redirect_url,
                $transaction,
                self::TOP_UP_TYPE
            );
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($payer_id);

        /**Execute the payment **/
        try {
            $result = $payment->execute($execution, $this->_api_context);

            $transaction_data = $transaction->transaction_data;

            $transaction_data = array_merge($transaction_data, [
                'token' => $token,
                'payerId' => $payer_id,
            ]);

            $transaction->transaction_data = $transaction_data;
            $transaction->save();
        } catch (\Exception $e) {
            $this->sendFailResponse(
                $redirect_url,
                $transaction,
                self::DONATION_TYPE
            );
        }

        if ($result->getState() == 'approved') {
            // update payee balance
            \Cache::tags(['transactions'])->flush();
            $transaction->status = Transaction::STATUS_SUCCESS;
            $transaction->save();

            return redirect($redirect_url . '#payment_status_ok');
        }

        return $this->sendFailResponse(
            $redirect_url,
            $transaction,
            self::DONATION_TYPE
        );
    }

    public function sendFailResponse($redirect_url, Transaction $transaction = null, $type = null)
    {
        if ($transaction) {
            $transaction->status = Transaction::STATUS_FAILED;
            $transaction->save();
        }
        return redirect($redirect_url . "#{$type}_status_fail");
    }
}
