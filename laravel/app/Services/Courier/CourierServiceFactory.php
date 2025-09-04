<?php

declare(strict_types=1);

namespace App\Services\Courier;

use App\Exceptions\CourierServiceException;
use App\Models\CourierService;
use App\Services\Courier\Providers\DhlService;
use App\Services\Courier\Providers\InPostService;

class CourierServiceFactory
{
    public function __construct(
        private InPostService $inPostService,
        private DhlService $dhlService
    ) {}

    /**
     * Create courier service instance by courier service ID
     */
    public function makeById(int $courierServiceId): CourierServiceInterface
    {
        $courierService = CourierService::find($courierServiceId);

        if (! $courierService) {
            throw new CourierServiceException("Courier service with ID {$courierServiceId} not found");
        }

        return $this->makeByCode($courierService->code);
    }

    /**
     * Create courier service instance by courier code
     */
    public function makeByCode(string $courierCode): CourierServiceInterface
    {
        return match ($courierCode) {
            'inpost' => $this->inPostService,
            'dhl' => $this->dhlService,
            default => throw new CourierServiceException("Unsupported courier code: {$courierCode}")
        };
    }

    /**
     * Get all available courier services
     */
    public function getAvailableCouriers(): array
    {
        return [
            'inpost' => $this->inPostService,
            'dhl' => $this->dhlService,
        ];
    }

    /**
     * Check if courier code is supported
     */
    public function isSupported(string $courierCode): bool
    {
        return in_array($courierCode, ['inpost', 'dhl']);
    }
}
