<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Shipment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Shipment $shipment
    ) {}
}

class ShipmentStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Shipment $shipment,
        public string $previousStatus
    ) {}
}

class PaymentCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public \App\Models\Payment $payment
    ) {}
}

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public \App\Models\Payment $payment
    ) {}
}