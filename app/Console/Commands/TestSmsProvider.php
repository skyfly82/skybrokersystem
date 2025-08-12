<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SMS\SmsManager;
use Illuminate\Console\Command;

class TestSmsProvider extends Command
{
    protected $signature = 'sms:test {phone} {message?}';
    protected $description = 'Test SMS provider with a test message';

    public function __construct(
        private SmsManager $smsManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'Test message from SkyBrokerSystem at ' . now()->format('Y-m-d H:i:s');

        $this->info("Sending test SMS to {$phone}...");
        $this->info("Message: {$message}");
        $this->info("Provider: " . config('sms.default'));

        try {
            $result = $this->smsManager->send($phone, $message);
            
            if ($result) {
                $this->info('✅ SMS sent successfully!');
                return self::SUCCESS;
            } else {
                $this->error('❌ SMS sending failed');
                return self::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}