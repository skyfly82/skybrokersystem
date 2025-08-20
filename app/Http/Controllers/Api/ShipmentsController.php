<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentsController extends Controller
{
    public function __construct(
        private ShipmentService $shipmentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        
        $query = $customer->shipments()->with(['courierService'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->tracking_number, function ($query, $tracking) {
                return $query->where('tracking_number', $tracking);
            });

        $shipments = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ShipmentResource::collection($shipments->items()),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'total' => $shipments->total(),
                'per_page' => $shipments->perPage(),
            ]
        ]);
    }

    public function store(CreateShipmentRequest $request): JsonResponse
    {
        try {
            $shipment = $this->shipmentService->createShipment(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'data' => new ShipmentResource($shipment),
                'message' => 'Shipment created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $shipment = $this->shipmentService->getShipmentByUuid($uuid);
        
        return response()->json([
            'success' => true,
            'data' => new ShipmentResource($shipment)
        ]);
    }

    public function track(string $trackingNumber): JsonResponse
    {
        try {
            $tracking = $this->shipmentService->trackShipment($trackingNumber);
            
            return response()->json([
                'success' => true,
                'data' => $tracking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function cancel(Shipment $shipment): JsonResponse
    {
        try {
            $this->shipmentService->cancelShipment($shipment);
            
            return response()->json([
                'success' => true,
                'message' => 'Shipment cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function label(Shipment $shipment): JsonResponse
    {
        try {
            $labelUrl = $this->shipmentService->getLabelUrl($shipment->tracking_number);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'label_url' => $labelUrl,
                    'expires_at' => now()->addHours(24)->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}