<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Services\SMS\SmsManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $message,
        public ?string $template = null,
        public array $variables = []
    ) {
        $this->onQueue('sms');
    }

    public function via(object $notifiable): array
    {
        return ['sms'];
    }

    public function toSms(object $notifiable): array
    {
        return [
            'to' => $notifiable->routeNotificationForSms(),
            'message' => $this->message,
            'template' => $this->template,
            'variables' => $this->variables,
        ];
    }
}