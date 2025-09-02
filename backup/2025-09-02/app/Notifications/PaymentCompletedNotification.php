<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment
    ) {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->getNotificationPreference('email', 'payment_completed')) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💰 Płatność zakończona - ' . number_format($this->payment->amount, 2) . ' PLN')
            ->view('emails.customer.payment-completed', [
                'payment' => $this->payment,
                'customer' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_completed',
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'method' => $this->payment->method,
            'type_label' => ucfirst($this->payment->type),
            'new_balance' => $this->payment->customer->current_balance,
            'message' => "Płatność {$this->payment->amount} {$this->payment->currency} została zakończona",
            'action_url' => route('customer.payments.show', $this->payment),
        ];
    }
}