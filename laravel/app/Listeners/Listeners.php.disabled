<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ShipmentCreated;
use App\Services\Notification\NotificationService;
use App\Mail\Customer\ShipmentCreatedMail;
use Illuminate\Support\Facades\Mail;

class SendShipmentCreatedNotification
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handle(ShipmentCreated $event): void
    {
        $shipment = $event->shipment;
        $customer = $shipment->customer;

        // Send email notification
        if ($customer->getNotificationPreference('email', 'shipment_created')) {
            $this->notificationService->sendNotification(
                $customer,
                'shipment_created',
                'email',
                [
                    'tracking_number' => $shipment->tracking_number,
                    'company_name' => $customer->company_name,
                    'recipient_name' => $shipment->recipient_data['name'],
                    'courier_name' => $shipment->courierService->name,
                ]
            );
        }

        // Send SMS notification if enabled
        if ($customer->getNotificationPreference('sms', 'shipment_created')) {
            $this->notificationService->sendNotification(
                $customer,
                'shipment_created',
                'sms',
                [
                    'tracking_number' => $shipment->tracking_number,
                    'courier_name' => $shipment->courierService->name,
                ]
            );
        }
    }
}

class SendPaymentCompletedNotification
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handle(\App\Events\PaymentCompleted $event): void
    {
        $payment = $event->payment;
        $customer = $payment->customer;

        // Send email notification
        if ($customer->getNotificationPreference('email', 'payment_completed')) {
            $this->notificationService->sendNotification(
                $customer,
                'payment_completed',
                'email',
                [
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'company_name' => $customer->company_name,
                    'payment_method' => ucfirst($payment->method),
                    'new_balance' => $customer->current_balance,
                ]
            );
        }
    }
}