<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Notification\Channels\EmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Notification $notification
    ) {
        $this->onQueue('emails');
    }

    public function handle(EmailChannel $emailChannel): void
    {
        try {
            if ($this->notification->status !== 'pending') {
                Log::info('Notification already processed', [
                    'notification_id' => $this->notification->id,
                    'status' => $this->notification->status,
                ]);

                return;
            }

            $notifiable = $this->notification->notifiable;
            $recipient = $notifiable->routeNotificationForMail();

            $success = $emailChannel->send(
                $recipient,
                $this->notification->title,
                $this->notification->message,
                $this->notification->data
            );

            if ($success) {
                $this->notification->markAsSent();
                Log::info('Email notification sent successfully', [
                    'notification_id' => $this->notification->id,
                    'recipient' => $recipient,
                ]);
            } else {
                throw new \Exception('Email sending failed');
            }

        } catch (\Exception $e) {
            Log::error('Email notification failed', [
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->notification->markAsFailed($e->getMessage());
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Email notification job failed permanently', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
        ]);

        $this->notification->markAsFailed($exception->getMessage());
    }
}
