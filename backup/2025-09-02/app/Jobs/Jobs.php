<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Notification\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds

    public function __construct(
        private Notification $notification
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        $notifiable = $this->notification->notifiable;
        $notificationService->processNotification($this->notification, $notifiable);
    }

    public function failed(\Throwable $exception): void
    {
        $this->notification->markAsFailed($exception->getMessage());
    }
}

class SendBulkNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $notifiableIds,
        private string $notifiableType,
        private string $templateName,
        private string $channel,
        private array $variables = []
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        $notifiableClass = $this->notifiableType;
        $notifiables = $notifiableClass::whereIn('id', $this->notifiableIds)->get();

        foreach ($notifiables as $notifiable) {
            $notificationService->sendNotification(
                $notifiable,
                $this->templateName,
                $this->channel,
                $this->variables
            );
        }
    }
}