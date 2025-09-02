<?php

declare(strict_types=1);

namespace App\Services\Notification\Channels;

use Illuminate\Support\Facades\Http;

class SmsChannel implements NotificationChannelInterface
{
    private string $apiUrl;
    private string $apiKey;
    private string $sender;

    public function __construct()
    {
        $this->apiUrl = config('sms.api_url');
        $this->apiKey = config('sms.api_key');
        $this->sender = config('sms.sender', 'SkyBroker');
    }

    public function send(string $recipient, string $subject, string $content, array $data = []): bool
    {
        // For development, log SMS instead of sending
        if (config('app.env') !== 'production') {
            return $this->logSms($recipient, $content);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/sms/send', [
                'to' => $this->formatPhoneNumber($recipient),
                'from' => $this->sender,
                'text' => $this->formatSmsContent($content),
            ]);

            if (!$response->successful()) {
                throw new \Exception('SMS API Error: ' . $response->body());
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('SMS notification failed', [
                'recipient' => $recipient,
                'content' => $content,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function sendTest(string $recipient, string $subject, string $content): bool
    {
        $testContent = '[TEST] ' . $content;
        return $this->send($recipient, $subject, $testContent);
    }

    private function logSms(string $recipient, string $content): bool
    {
        \Log::info('SMS Notification (Development Mode)', [
            'to' => $recipient,
            'content' => $content,
            'timestamp' => now()->toISOString()
        ]);

        return true;
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add +48 prefix if not present (Polish numbers)
        if (!str_starts_with($phone, '48') && strlen($phone) === 9) {
            $phone = '48' . $phone;
        }
        
        return '+' . $phone;
    }

    private function formatSmsContent(string $content): string
    {
        // SMS content should be max 160 characters
        if (strlen($content) > 160) {
            return substr($content, 0, 157) . '...';
        }
        
        return $content;
    }
}