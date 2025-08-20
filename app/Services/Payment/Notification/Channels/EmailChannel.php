<?php

declare(strict_types=1);

namespace App\Services\Notification\Channels;

use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;

class EmailChannel implements NotificationChannelInterface
{
    public function send(string $recipient, string $subject, string $content, array $data = []): bool
    {
        try {
            Mail::to($recipient)->send(new GenericNotificationMail($subject, $content, $data));
            return true;
        } catch (\Exception $e) {
            \Log::error('Email notification failed', [
                'recipient' => $recipient,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function sendTest(string $recipient, string $subject, string $content): bool
    {
        return $this->send($recipient, '[TEST] ' . $subject, $content);
    }
}