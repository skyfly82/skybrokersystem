<?php

declare(strict_types=1);

namespace App\Services\SMS\Providers;

interface SmsProviderInterface
{
    public function send(string $to, string $message, array $options = []): bool;
    
    public function getBalance(): ?float;
    
    public function isAvailable(): bool;
}