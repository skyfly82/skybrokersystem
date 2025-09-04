<?php

declare(strict_types=1);

namespace App\Services\SMS;

use App\Exceptions\SmsException;
use App\Services\SMS\Providers\LogProvider;
use App\Services\SMS\Providers\SmsApiProvider;
use App\Services\SMS\Providers\SmsProviderInterface;
use App\Services\SMS\Providers\TwilioProvider;
use App\Services\SMS\Providers\VonageProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmsManager
{
    private array $providers = [
        'log' => LogProvider::class,
        'smsapi' => SmsApiProvider::class,
        'twilio' => TwilioProvider::class,
        'vonage' => VonageProvider::class,
    ];

    public function send(string $to, string $message, array $options = []): bool
    {
        // Validate phone number
        $to = $this->formatPhoneNumber($to);

        // Check rate limits
        if (! $this->checkRateLimit($to)) {
            throw new SmsException('Rate limit exceeded for phone number: '.$to);
        }

        // Validate message length
        $message = $this->prepareMessage($message);

        $provider = $this->getProvider();

        try {
            $result = $provider->send($to, $message, $options);

            // Log successful send
            Log::info('SMS sent successfully', [
                'to' => $to,
                'provider' => config('sms.default'),
                'length' => strlen($message),
            ]);

            // Update rate limit counter
            $this->updateRateLimit($to);

            return $result;

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'to' => $to,
                'provider' => config('sms.default'),
                'error' => $e->getMessage(),
            ]);

            throw new SmsException('Failed to send SMS: '.$e->getMessage(), 0, $e);
        }
    }

    public function sendFromTemplate(string $to, string $template, array $variables = []): bool
    {
        $message = $this->renderTemplate($template, $variables);

        return $this->send($to, $message);
    }

    public function sendBulk(array $recipients, string $message): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            try {
                $results[$recipient] = [
                    'success' => $this->send($recipient, $message),
                    'error' => null,
                ];
            } catch (\Exception $e) {
                $results[$recipient] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function getProvider(): SmsProviderInterface
    {
        $providerName = config('sms.default');

        if (! isset($this->providers[$providerName])) {
            throw new SmsException("Unsupported SMS provider: {$providerName}");
        }

        return app($this->providers[$providerName]);
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add +48 prefix if not present (Polish numbers)
        if (! str_starts_with($phone, '48') && strlen($phone) === 9) {
            $phone = '48'.$phone;
        }

        // Validate phone number format
        if (! preg_match('/^48[0-9]{9}$/', $phone)) {
            throw new SmsException('Invalid phone number format: '.$phone);
        }

        return '+'.$phone;
    }

    private function prepareMessage(string $message): string
    {
        $maxLength = config('sms.settings.max_length', 160);

        if (strlen($message) > $maxLength) {
            $message = substr($message, 0, $maxLength - 3).'...';
        }

        return $message;
    }

    private function renderTemplate(string $templateName, array $variables): string
    {
        $template = config("sms.templates.{$templateName}");

        if (! $template) {
            throw new SmsException("SMS template not found: {$templateName}");
        }

        foreach ($variables as $key => $value) {
            $template = str_replace('{'.$key.'}', $value, $template);
        }

        return $template;
    }

    private function checkRateLimit(string $phone): bool
    {
        $key = 'sms_rate_limit:'.$phone;
        $limits = config('sms.rate_limits');

        // Check per minute limit
        $minuteKey = $key.':minute:'.now()->format('Y-m-d H:i');
        $minuteCount = Cache::get($minuteKey, 0);
        if ($minuteCount >= $limits['per_minute']) {
            return false;
        }

        // Check per hour limit
        $hourKey = $key.':hour:'.now()->format('Y-m-d H');
        $hourCount = Cache::get($hourKey, 0);
        if ($hourCount >= $limits['per_hour']) {
            return false;
        }

        // Check per day limit
        $dayKey = $key.':day:'.now()->format('Y-m-d');
        $dayCount = Cache::get($dayKey, 0);
        if ($dayCount >= $limits['per_day']) {
            return false;
        }

        return true;
    }

    private function updateRateLimit(string $phone): void
    {
        $key = 'sms_rate_limit:'.$phone;

        // Update minute counter
        $minuteKey = $key.':minute:'.now()->format('Y-m-d H:i');
        Cache::increment($minuteKey);
        Cache::expire($minuteKey, 60);

        // Update hour counter
        $hourKey = $key.':hour:'.now()->format('Y-m-d H');
        Cache::increment($hourKey);
        Cache::expire($hourKey, 3600);

        // Update day counter
        $dayKey = $key.':day:'.now()->format('Y-m-d');
        Cache::increment($dayKey);
        Cache::expire($dayKey, 86400);
    }
}
