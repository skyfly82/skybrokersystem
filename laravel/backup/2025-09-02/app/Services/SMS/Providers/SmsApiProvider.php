<?php

declare(strict_types=1);

namespace App\Services\SMS\Providers;

use App\Exceptions\SmsException;
use Illuminate\Support\Facades\Http;

class SmsApiProvider implements SmsProviderInterface
{
    private string $apiUrl;

    private string $token;

    private string $sender;

    private bool $testMode;

    public function __construct()
    {
        $config = config('sms.providers.smsapi');
        $this->apiUrl = $config['api_url'];
        $this->token = $config['api_token'];
        $this->sender = $config['sender'];
        $this->testMode = $config['test_mode'];
    }

    public function send(string $to, string $message, array $options = []): bool
    {
        $url = $this->apiUrl.'/sms.do';

        $data = [
            'to' => $to,
            'message' => $message,
            'from' => $options['sender'] ?? $this->sender,
            'format' => 'json',
        ];

        if ($this->testMode) {
            $data['test'] = '1';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->asForm()->post($url, $data);

        if (! $response->successful()) {
            throw new SmsException('SMSAPI request failed: '.$response->body());
        }

        $result = $response->json();

        if (isset($result['error'])) {
            throw new SmsException('SMSAPI error: '.$result['message']);
        }

        return true;
    }

    public function getBalance(): ?float
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->token,
            ])->get($this->apiUrl.'/profile.do', ['format' => 'json']);

            if ($response->successful()) {
                $data = $response->json();

                return $data['points'] ?? null;
            }
        } catch (\Exception $e) {
            // Ignore balance check errors
        }

        return null;
    }

    public function isAvailable(): bool
    {
        return ! empty($this->token);
    }
}
