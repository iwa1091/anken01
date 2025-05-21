<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed.', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // イベントタイプで処理を分岐
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                Log::info('Checkout session completed', ['session' => $session]);
                // ここで注文ステータスを更新などの処理を書く
                break;

            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                Log::info('Payment succeeded', ['payment_intent' => $intent]);
                // ここに支払い成功時の処理を書く
                break;

            // 他のイベントタイプも必要に応じて追加

            default:
                Log::warning('Unhandled event type: ' . $event->type);
        }

        return response('Webhook received', 200);
    }
}
