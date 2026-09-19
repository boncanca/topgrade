<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signatureHeader = (string) $request->header('Stripe-Signature', '');

        if (! $this->paymentService->verifyWebhookSignature($rawPayload, $signatureHeader)) {
            Log::warning('Rejected Stripe webhook due to invalid or missing signature header.');

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payload = json_decode($rawPayload, true);
        if (! is_array($payload)) {
            return response()->json(['error' => 'Invalid JSON payload'], 400);
        }

        $eventId = (string) ($payload['id'] ?? '');
        $eventType = (string) ($payload['type'] ?? 'unknown');

        if (empty($eventId)) {
            return response()->json(['error' => 'Missing event identifier'], 400);
        }

        // Atomic deduplication via unique constraint on event_id
        try {
            DB::table('webhook_calls')->insert([
                'gateway' => 'stripe',
                'event_id' => $eventId,
                'event_type' => $eventType,
                'summary' => "Event {$eventType} for {$eventId}",
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException|QueryException $e) {
            Log::info("Duplicate Stripe webhook ignored: {$eventId}");

            return response()->json([
                'status' => 'already_processed',
                'event_id' => $eventId,
            ], 200);
        }

        $handled = $this->paymentService->handleWebhook($payload, $signatureHeader);

        return response()->json([
            'status' => $handled ? 'success' : 'failed',
            'event_id' => $eventId,
        ], 200);
    }
}
