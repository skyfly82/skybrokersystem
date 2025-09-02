<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CustomerUser;
use App\Models\Shipment;

class ShipmentPolicy
{
    public function view(CustomerUser $user, Shipment $shipment): bool
    {
        return $user->customer_id === $shipment->customer_id;
    }

    public function update(CustomerUser $user, Shipment $shipment): bool
    {
        // Only allow updates for shipments belonging to user's customer
        // and only if shipment is in editable state
        return $user->customer_id === $shipment->customer_id && $shipment->isEditable();
    }

    public function delete(CustomerUser $user, Shipment $shipment): bool
    {
        // Only allow deletion for shipments belonging to user's customer
        // and only if shipment can be cancelled
        return $user->customer_id === $shipment->customer_id && $shipment->canBeCancelled();
    }
}
