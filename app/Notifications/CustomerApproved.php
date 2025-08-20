<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Customer $customer
    ) {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Twoje konto zostało zatwierdzone - SkyBrokerSystem')
            ->greeting('Witaj ' . $notifiable->full_name . '!')
            ->line('Mamy przyjemność poinformować, że Twoje konto firmowe zostało zatwierdzone przez nasz zespół.')
            ->line('**Dane Twojego konta:**')
            ->line('• Firma: ' . $this->customer->company_name)
            ->line('• Email: ' . $this->customer->email)
            ->line('• Status: Aktywne ✅')
            ->line('Możesz teraz w pełni korzystać ze wszystkich funkcji systemu:')
            ->line('✅ Tworzenie przesyłek')
            ->line('✅ Śledzenie przesyłek')
            ->line('✅ Zarządzanie płatnościami')
            ->line('✅ Generowanie raportów')
            ->action('Zaloguj się do panelu', route('customer.login'))
            ->line('Dziękujemy za wybór SkyBrokerSystem!')
            ->line('W razie pytań skontaktuj się z naszym działem obsługi klienta.')
            ->salutation('Zespół SkyBrokerSystem');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'customer_approved',
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->company_name,
            'message' => 'Twoje konto zostało zatwierdzone i jest aktywne',
            'action_url' => route('customer.login'),
        ];
    }
}