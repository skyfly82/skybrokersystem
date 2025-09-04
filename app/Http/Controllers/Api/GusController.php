<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GusController extends Controller
{
    public function __construct(
        private readonly GusService $gusService
    ) {}

    public function getCompanyByNip(string $nip): JsonResponse
    {
        try {
            // Validate NIP format
            $nip = preg_replace('/[^0-9]/', '', $nip);
            
            if (strlen($nip) !== 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nieprawidłowy format NIP. NIP musi mieć 10 cyfr.',
                ], 400);
            }

            $companyData = $this->gusService->getCompanyByNip($nip);

            if (!$companyData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nie znaleziono firmy o podanym NIP w bazie GUS.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $companyData,
            ]);

        } catch (\Exception $e) {
            \Log::error('GUS API error: ' . $e->getMessage(), [
                'nip' => $nip,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas pobierania danych z GUS. Spróbuj ponownie później.',
            ], 500);
        }
    }
}
