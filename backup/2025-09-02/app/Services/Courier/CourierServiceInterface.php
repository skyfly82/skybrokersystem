<?php

declare(strict_types=1);

namespace App\Services\Courier;

interface CourierServiceInterface
{
    public function createShipment(array $data): array;
    
    public function trackShipment(string $trackingNumber): array;
    
    public function cancelShipment(string $trackingNumber): bool;
    
    public function getLabel(string $trackingNumber, ?string $format = null, ?string $size = null): string;
    
    public function calculatePrice(array $data): array;
    
    public function getAvailableServices(): array;
    
    public function getId(): int;
    
    public function handleTrackingWebhook(array $data): array;
}