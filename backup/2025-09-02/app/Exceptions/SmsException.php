<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class SmsException extends Exception
{
    public static function rateLimitExceeded(string $phone): self
    {
        return new self("SMS rate limit exceeded for phone number: {$phone}");
    }

    public static function invalidPhoneNumber(string $phone): self
    {
        return new self("Invalid phone number format: {$phone}");
    }

    public static function providerNotAvailable(string $provider): self
    {
        return new self("SMS provider not available: {$provider}");
    }

    public static function templateNotFound(string $template): self
    {
        return new self("SMS template not found: {$template}");
    }

    public static function messageTooLong(int $length, int $maxLength): self
    {
        return new self("SMS message too long: {$length} characters (max: {$maxLength})");
    }
}
