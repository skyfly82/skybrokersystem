<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Services\SMS\SmsManager;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(
        private SmsManager $smsManager
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSms($notifiable);

        $to = $message['to'];

        if (isset($message['template'])) {
            $this->smsManager->sendFromTemplate(
                $to,
                $message['template'],
                $message['variables'] ?? []
            );
        } else {
            $this->smsManager->send($to, $message['message']);
        }
    }
}
