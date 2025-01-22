<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class StripeController extends Controller
{
    public function success() {

    }

    public function failure() {

    }

    public function webhook(Request $request) {
        $stripe = new \Stripe\StripeClient(config('app.stripe.secret_key'));

        $endpoint_secret = config('app.stripe.endpoint_secret');
        $payload = $request->getContent();
        $sig_header = request()->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error($e);

            return Response('Invalid Payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error($e);

            return Response('Invalid Payload', 400);
        }

        Log::info('===============================');
        Log::info('===============================');
        Log::info($event->type);
        Log::info($event);

        switch ($event->type) {
            case 'charge.updated':
                $charge = $event->data->object;
                $transactionID = $charge['balance_transaction'];
                $paymentIntent = $charge['payment_intent'];
                $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionID);

                $totalAmount = $balanceTransaction['amount'];
                $stripeFee = 0;

                foreach ($balanceTransaction['fee_details'] as $fee_detail) {
                    if ($fee_detail['type'] === 'stripe_fee') {
                        $stripeFee = $fee_detail['amount'];
                    }
                }
                $platformFeePercent = config('app.platform_fee_pct');

                $orders = Order::where('payment_intent', $paymentIntent)->get();

                foreach ($orders as $order) {
                    $vendorShare = $order->total_price / $totalAmount;

                    $order->online_payment_commission = $vendorShare;
                }
            case 'checkout.session.completed':

        }
    }
}
