<?php

declare(strict_types=1);

namespace App\Services\SMS\Providers;

use Twilio\Rest\Client;
use App\Exceptions\SmsException;

class TwilioProvider implements SmsProviderInterface
{
    private Client $client;
    private string $from;
    
    public function __construct()
    {
        $config = config('sms.providers.twilio');
        
        if (empty($config['account_sid']) || empty($config['auth_token'])) {
            throw new SmsException('Twilio credentials not configured');
        }
        
        $this->client = new Client($config['account_sid'], $config['auth_token']);
        $this->from = $config['from'];
    }
    
    public function send(string $to, string $message, array $options = []): bool
    {
        try {
            $this->client->messages->create($to, [
                'from' => $options['from'] ?? $this->from,
                'body' => $message,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            throw new SmsException('Twilio error: ' . $e->getMessage(), 0, $e);
        }
    }
    
    public function getBalance(): ?float
    {
        try {
            $account = $this->client->api->v2010->accounts->get();
            return (float) $account->balance;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function isAvailable(): bool
    {
        return !empty($this->from);
    }
}