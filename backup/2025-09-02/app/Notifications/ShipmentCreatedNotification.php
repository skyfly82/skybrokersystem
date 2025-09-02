<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shipment $shipment
    ) {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->getNotificationPreference('email', 'shipment_created')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📦 Przesyłka utworzona - '.$this->shipment->tracking_number)
            ->view('emails.customer.shipment-created', [
                'shipment' => $this->shipment,
                'customer' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shipment_created',
            'shipment_id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'courier' => $this->shipment->courierService->name,
            'recipient' => $this->shipment->recipient_data['name'],
            'cost' => $this->shipment->cost_data['gross'] ?? 0,
            'message' => "Przesyłka {$this->shipment->tracking_number} została utworzona",
            'action_url' => route('customer.shipments.show', $this->shipment),
        ];
    }
}
