<?php

declare(strict_types=1);

namespace App\Mail\Customer;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShipmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Shipment $shipment
    ) {}

    public function build()
    {
        return $this->subject("Przesyłka utworzona - {$this->shipment->tracking_number}")
            ->view('emails.customer.shipment-created')
            ->with(['shipment' => $this->shipment]);
    }
}

class ShipmentDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Shipment $shipment
    ) {}

    public function build()
    {
        return $this->subject("Przesyłka dostarczona - {$this->shipment->tracking_number}")
            ->view('emails.customer.shipment-delivered')
            ->with(['shipment' => $this->shipment]);
    }
}

class PaymentCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public \App\Models\Payment $payment
    ) {}

    public function build()
    {
        return $this->subject("Płatność zakończona - {$this->payment->amount} PLN")
            ->view('emails.customer.payment-completed')
            ->with(['payment' => $this->payment]);
    }
}
