<?php

declare(strict_types=1);

namespace App\Services\SMS\Providers;

use Illuminate\Support\Facades\Log;

class LogProvider implements SmsProviderInterface
{
    public function send(string $to, string $message, array $options = []): bool
    {
        Log::channel(config('sms.providers.log.channel', 'sms'))->info('SMS Notification', [
            'to' => $to,
            'message' => $message,
            'options' => $options,
            'timestamp' => now()->toISOString(),
            'provider' => 'log',
        ]);

        return true;
    }

    public function getBalance(): ?float
    {
        return null; // Log provider doesn't have balance
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
