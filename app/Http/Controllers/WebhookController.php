<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CustomerComplaint;
use App\Models\CustomerServiceIntegration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function freshdeskWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            
            Log::info('Freshdesk webhook received', $payload);

            if (!$this->validateFreshdeskSignature($request)) {
                Log::warning('Invalid Freshdesk webhook signature');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $eventType = $request->header('X-Freshworks-Webhook-Event-Type');
            
            switch ($eventType) {
                case 'ticket_created':
                    $this->handleFreshdeskTicketCreated($payload);
                    break;
                    
                case 'ticket_updated':
                    $this->handleFreshdeskTicketUpdated($payload);
                    break;
                    
                case 'note_created':
                    $this->handleFreshdeskNoteCreated($payload);
                    break;
                    
                default:
                    Log::info("Unhandled Freshdesk event type: {$eventType}");
            }

            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            Log::error('Freshdesk webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function freshcallerWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            
            Log::info('Freshcaller webhook received', $payload);

            if (!$this->validateFreshcallerSignature($request)) {
                Log::warning('Invalid Freshcaller webhook signature');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $eventType = $request->header('X-Freshworks-Webhook-Event-Type');
            
            switch ($eventType) {
                case 'call_completed':
                    $this->handleFreshcallerCallCompleted($payload);
                    break;
                    
                case 'call_recording_ready':
                    $this->handleFreshcallerRecordingReady($payload);
                    break;
                    
                default:
                    Log::info("Unhandled Freshcaller event type: {$eventType}");
            }

            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            Log::error('Freshcaller webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function validateFreshdeskSignature(Request $request): bool
    {
        $integration = CustomerServiceIntegration::where('service_name', 'freshdesk')
                                                 ->where('is_active', true)
                                                 ->first();
        
        if (!$integration || !$integration->webhook_secret) {
            return false;
        }

        $signature = $request->header('X-Freshworks-Webhook-Signature');
        if (!$signature) {
            return false;
        }

        $expectedSignature = base64_encode(hash_hmac(
            'sha256', 
            $request->getContent(), 
            $integration->webhook_secret,
            true
        ));

        return hash_equals($expectedSignature, $signature);
    }

    private function validateFreshcallerSignature(Request $request): bool
    {
        $integration = CustomerServiceIntegration::where('service_name', 'freshcaller')
                                                 ->where('is_active', true)
                                                 ->first();
        
        if (!$integration || !$integration->webhook_secret) {
            return false;
        }

        $signature = $request->header('X-Freshworks-Webhook-Signature');
        if (!$signature) {
            return false;
        }

        $expectedSignature = base64_encode(hash_hmac(
            'sha256', 
            $request->getContent(), 
            $integration->webhook_secret,
            true
        ));

        return hash_equals($expectedSignature, $signature);
    }

    private function handleFreshdeskTicketCreated(array $payload): void
    {
        $ticket = $payload['ticket'] ?? null;
        if (!$ticket) {
            return;
        }

        // Try to find existing complaint by external reference
        $complaint = CustomerComplaint::where('freshdesk_ticket_id', $ticket['id'])->first();
        
        if ($complaint) {
            $complaint->update([
                'freshdesk_data' => $payload,
                'status' => $this->mapFreshdeskStatus($ticket['status']),
            ]);
            
            Log::info("Updated complaint {$complaint->complaint_number} from Freshdesk ticket {$ticket['id']}");
        } else {
            Log::info("No matching complaint found for Freshdesk ticket {$ticket['id']}");
        }
    }

    private function handleFreshdeskTicketUpdated(array $payload): void
    {
        $ticket = $payload['ticket'] ?? null;
        if (!$ticket) {
            return;
        }

        $complaint = CustomerComplaint::where('freshdesk_ticket_id', $ticket['id'])->first();
        
        if ($complaint) {
            $updates = [
                'freshdesk_data' => $payload,
                'status' => $this->mapFreshdeskStatus($ticket['status']),
            ];

            // If ticket is resolved in Freshdesk, mark complaint as resolved
            if ($ticket['status'] == 4 || $ticket['status'] == 5) { // Resolved or Closed
                $updates['resolved_at'] = now();
                $updates['resolution'] = $ticket['description_text'] ?? 'Resolved via Freshdesk';
            }

            $complaint->update($updates);
            
            Log::info("Updated complaint {$complaint->complaint_number} from Freshdesk ticket update");
        }
    }

    private function handleFreshdeskNoteCreated(array $payload): void
    {
        $note = $payload['note'] ?? null;
        $ticket = $payload['ticket'] ?? null;
        
        if (!$note || !$ticket) {
            return;
        }

        $complaint = CustomerComplaint::where('freshdesk_ticket_id', $ticket['id'])->first();
        
        if ($complaint && !$note['private']) {
            // Add public note as a message to complaint
            $complaint->messages()->create([
                'sender_type' => 'admin',
                'sender_id' => 1, // Default system user
                'message' => $note['body_text'] ?? $note['body'],
                'is_internal' => false,
                'created_at' => $note['created_at'] ?? now()
            ]);
            
            Log::info("Added note to complaint {$complaint->complaint_number} from Freshdesk");
        }
    }

    private function handleFreshcallerCallCompleted(array $payload): void
    {
        $call = $payload['call'] ?? null;
        if (!$call) {
            return;
        }

        // Log call for potential complaint follow-up
        Log::info('Freshcaller call completed', [
            'call_id' => $call['id'],
            'caller_number' => $call['caller_number'] ?? null,
            'duration' => $call['duration'] ?? null,
            'call_type' => $call['call_type'] ?? null
        ]);

        // Future: Could automatically create complaint from call if configured
    }

    private function handleFreshcallerRecordingReady(array $payload): void
    {
        $call = $payload['call'] ?? null;
        if (!$call) {
            return;
        }

        Log::info('Freshcaller recording ready', [
            'call_id' => $call['id'],
            'recording_url' => $call['recording_url'] ?? null
        ]);

        // Future: Could attach recording to related complaint
    }

    private function mapFreshdeskStatus(int $status): string
    {
        return match ($status) {
            2 => 'open',           // Open
            3 => 'in_progress',    // Pending
            4 => 'resolved',       // Resolved
            5 => 'closed',         // Closed
            6 => 'waiting_customer', // Waiting on Customer
            7 => 'waiting_customer', // Waiting on Third Party
            default => 'open'
        };
    }
}