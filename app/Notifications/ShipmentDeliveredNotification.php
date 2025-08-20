<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentDeliveredNotification extends Notification implements ShouldQueue
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
        
        if ($notifiable->getNotificationPreference('email', 'shipment_delivered')) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Przesyłka dostarczona - ' . $this->shipment->tracking_number)
            ->view('emails.customer.shipment-delivered', [
                'shipment' => $this->shipment,
                'customer' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shipment_delivered',
            'shipment_id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'courier' => $this->shipment->courierService->name,
            'recipient' => $this->shipment->recipient_data['name'],
            'delivered_at' => $this->shipment->delivered_at->toISOString(),
            'delivery_time' => $this->shipment->created_at->diffInHours($this->shipment->delivered_at) . ' godzin',
            'message' => "Przesyłka {$this->shipment->tracking_number} została dostarczona",
            'action_url' => route('customer.shipments.show', $this->shipment),
        ];
    }
}