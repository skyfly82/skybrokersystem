<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourierServiceResource;
use App\Models\CourierService;
use App\Services\Courier\CourierServiceFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouriersController extends Controller
{
    public function __construct(
        private CourierServiceFactory $courierFactory
    ) {}

    public function index(): JsonResponse
    {
        $couriers = CourierService::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CourierServiceResource::collection($couriers)
        ]);
    }

    public function show(CourierService $courier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CourierServiceResource($courier)
        ]);
    }

    public function services(CourierService $courier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $courier->supported_services
        ]);
    }

    public function pickupPoints(Request $request, CourierService $courier): JsonResponse
    {
        $request->validate([
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
        ]);

        try {
            $courierService = $this->courierFactory->makeByCode($courier->code);
            
            if (!method_exists($courierService, 'getPickupPoints')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pickup points not supported for this courier'
                ], 400);
            }

            $points = $courierService->getPickupPoints([
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            return response()->json([
                'success' => true,
                'data' => $points
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function calculatePrice(Request $request, CourierService $courier): JsonResponse
    {
        $request->validate([
            'sender' => 'required|array',
            'recipient' => 'required|array',
            'package' => 'required|array',
            'service_type' => 'nullable|string',
        ]);

        try {
            $courierService = $this->courierFactory->makeByCode($courier->code);
            $prices = $courierService->calculatePrice($request->all());

            return response()->json([
                'success' => true,
                'data' => $prices
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}