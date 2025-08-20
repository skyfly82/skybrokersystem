<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Services\SMS\SmsManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Notification $notification
    ) {
        $this->onQueue('sms');
    }

    public function handle(SmsManager $smsManager): void
    {
        try {
            if ($this->notification->status !== 'pending') {
                Log::info('SMS notification already processed', [
                    'notification_id' => $this->notification->id,
                    'status' => $this->notification->status
                ]);
                return;
            }

            $notifiable = $this->notification->notifiable;
            $recipient = $notifiable->routeNotificationForSms();
            
            if (!$recipient) {
                throw new \Exception('No SMS recipient available');
            }

            $success = $smsManager->send($recipient, $this->notification->message);

            if ($success) {
                $this->notification->markAsSent();
                Log::info('SMS notification sent successfully', [
                    'notification_id' => $this->notification->id,
                    'recipient' => $recipient
                ]);
            } else {
                throw new \Exception('SMS sending failed');
            }

        } catch (\Exception $e) {
            Log::error('SMS notification failed', [
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->notification->markAsFailed($e->getMessage());
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SMS notification job failed permanently', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage()
        ]);

        $this->notification->markAsFailed($exception->getMessage());
    }
}