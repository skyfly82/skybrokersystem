<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Customer $customer,
        public float $threshold = 100.00
    ) {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->getNotificationPreference('email', 'low_balance')) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Niskie saldo konta - SkyBrokerSystem')
            ->greeting('Dzień dobry!')
            ->line('Informujemy, że saldo Twojego konta jest niskie.')
            ->line('**Aktualny stan konta:**')
            ->line('• Dostępne środki: **' . number_format($this->customer->current_balance, 2) . ' PLN**')
            ->line('• Limit kredytowy: ' . number_format($this->customer->credit_limit, 2) . ' PLN')
            ->line('• Łączne dostępne środki: ' . number_format($this->customer->current_balance + $this->customer->credit_limit, 2) . ' PLN')
            ->line('Aby uniknąć problemów z realizacją przesyłek, zalecamy doładowanie konta.')
            ->action('Doładuj konto', route('customer.payments.topup'))
            ->line('Doładowanie konta pozwoli Ci na:')
            ->line('✅ Bezproblemowe tworzenie nowych przesyłek')
            ->line('✅ Korzystanie z wszystkich usług kurierskich')
            ->line('✅ Dostęp do promocyjnych rabatów')
            ->line('W razie pytań skontaktuj się z naszym działem obsługi klienta.')
            ->salutation('Zespół SkyBrokerSystem');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_balance',
            'customer_id' => $this->customer->id,
            'current_balance' => $this->customer->current_balance,
            'threshold' => $this->threshold,
            'credit_limit' => $this->customer->credit_limit,
            'message' => "Niskie saldo konta: {$this->customer->current_balance} PLN",
            'action_url' => route('customer.payments.topup'),
            'priority' => 'high',
        ];
    }
}
