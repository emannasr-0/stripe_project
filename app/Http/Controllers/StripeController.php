<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $session = CheckoutSession::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 2000, // Amount in cents
                ],
                'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
       'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',

        'cancel_url' => route('payment.cancel'),
    ]);

    return redirect($session->url);
}

public function success(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $sessionId = $request->query('session_id');

   // dd($sessionId); // Debug the session ID

    try {
        // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        // $session = $stripe->checkout->sessions->retrieve($sessionId);
        $session = CheckoutSession::retrieve($sessionId);
// dd($session);
        $payment = Payment::create([
            'stripe_id' => $session->id,
            'amount' => $session->amount_total,
            'currency' => $session->currency,
            'status' => $session->status,
        ]);

       // dd($payment); // Debug the payment object

        return view('success');
    } catch (\Exception $e) {
        // Handle the exception
      // dd($e);
    }
}


    public function cancel()
    {
        return view('cancel');
    }


    public function handleStripe(Request $request)
    {
        // Set your Stripe secret key
        Stripe::setApiKey(config('services.stripe.secret'));

        $endpoint_secret = config('services.stripe.webhook_secret');
                            // Retrieve this from Stripe Dashboard

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            // Handle the event based on its type
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    // Handle successful payment
                    // Update order status, etc.
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    // Handle failed payment
                    // Update order status, etc.
                    break;

                // Handle other event types as needed
                default:
                    // Unexpected event type
                    return response()->json(['status' => 'unexpected event type'], 400);
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['status' => 'invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['status' => 'invalid signature'], 400);
        }

        // Return a success response
        return response()->json(['status' => 'success'], 200);
    }
}