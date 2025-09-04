<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerRegisteredNotification extends Notification implements ShouldQueue
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
            ->subject('🔔 Nowa rejestracja klienta - '.$this->customer->company_name)
            ->view('emails.admin.customer-registered', [
                'customer' => $this->customer,
                'admin' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'customer_registered',
            'customer_id' => $this->customer->id,
            'company_name' => $this->customer->company_name,
            'email' => $this->customer->email,
            'nip' => $this->customer->nip,
            'created_at' => $this->customer->created_at->toISOString(),
            'message' => "Nowy klient oczekuje na zatwierdzenie: {$this->customer->company_name}",
            'action_url' => route('admin.customers.show', $this->customer),
            'priority' => 'high',
        ];
    }
}
