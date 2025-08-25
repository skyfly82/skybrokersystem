<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerVerificationCode extends Notification
{
    use Queueable;

    public function __construct(
        public Customer $customer,
        public array $verificationData
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = route('customer.verify', ['token' => $this->verificationData['token']]);
        $codeExpiryMinutes = \App\Models\SystemSetting::get('verification_code_expiry_minutes', 60);
        
        return (new MailMessage)
            ->subject('🔐 Weryfikacja konta - SkyBrokerSystem')
            ->greeting('Witaj!')
            ->line('Dziękujemy za rejestrację w SkyBrokerSystem.')
            ->line('**Dane Twojego konta:**')
            ->line('• Firma: ' . $this->customer->company_name)
            ->line('• Email: ' . $this->customer->email)
            ->line('**Aby aktywować konto, wprowadź 6-cyfrowy kod weryfikacyjny:**')
            ->line('# **' . $this->verificationData['code'] . '**')
            ->line("⏰ Kod jest ważny przez {$codeExpiryMinutes} minut.")
            ->line('**Opcja 1: Wprowadź kod automatycznie**')
            ->action('Kliknij tutaj i wprowadź kod', $verificationUrl)
            ->line('**Opcja 2: Wprowadź kod ręcznie**')
            ->line('Jeśli nie możesz kliknąć w przycisk, skopiuj i wklej ten link:')
            ->line($verificationUrl)
            ->line('**Instrukcje:**')
            ->line('1. Kliknij w przycisk powyżej lub przejdź pod podany link')
            ->line('2. Wprowadź 6-cyfrowy kod weryfikacyjny')
            ->line('3. Twoje konto zostanie aktywowane automatycznie')
            ->line('4. Możesz się zalogować i korzystać z systemu')
            ->line('**Ważne:** Link jest ważny przez ' . \App\Models\SystemSetting::get('verification_link_expiry_hours', 24) . ' godzin.')
            ->line('Jeśli nie rejestrowałeś się w naszym systemie, zignoruj tę wiadomość.')
            ->salutation('Zespół SkyBrokerSystem');
    }
}
