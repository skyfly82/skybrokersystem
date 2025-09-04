<?php

declare(strict_types=1);

namespace App\Services\Notification\Channels;

interface NotificationChannelInterface
{
    public function send(string $recipient, string $subject, string $content, array $data = []): bool;

    public function sendTest(string $recipient, string $subject, string $content): bool;
}
