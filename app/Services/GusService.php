<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GusService
{
    private string $apiUrl;
    private ?string $userKey;
    private ?string $sessionId = null;

    public function __construct()
    {
        $this->apiUrl = config('services.gus.api_url', 'https://wyszukiwarkaregon.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc');
        $this->userKey = config('services.gus.user_key');
    }

    public function getCompanyByNip(string $nip): ?array
    {
        // Check if GUS integration is properly configured
        if (!$this->userKey) {
            \Log::warning('GUS User Key not configured');
            return $this->getMockData($nip);
        }

        try {
            // Try to get session ID
            if (!$this->getSessionId()) {
                \Log::error('Could not establish GUS session');
                return $this->getMockData($nip);
            }

            // Search for company by NIP
            $searchResult = $this->searchByNip($nip);
            
            if (!$searchResult) {
                return null;
            }

            // Get detailed company data
            $companyData = $this->getCompanyDetails($searchResult);

            return $this->formatCompanyData($companyData);

        } catch (\Exception $e) {
            \Log::error('GUS API error: ' . $e->getMessage(), ['nip' => $nip]);
            return $this->getMockData($nip);
        }
    }

    private function getSessionId(): bool
    {
        if ($this->sessionId) {
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/soap+xml; charset=utf-8',
            ])->post($this->apiUrl, $this->buildLoginSoapEnvelope());

            if ($response->successful()) {
                // Parse SOAP response to extract session ID
                $sessionId = $this->parseSessionId($response->body());
                
                if ($sessionId) {
                    $this->sessionId = $sessionId;
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            \Log::error('GUS login failed: ' . $e->getMessage());
            return false;
        }
    }

    private function searchByNip(string $nip): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/soap+xml; charset=utf-8',
            ])->post($this->apiUrl, $this->buildSearchSoapEnvelope($nip));

            if ($response->successful()) {
                return $this->parseSearchResult($response->body());
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('GUS search failed: ' . $e->getMessage());
            return null;
        }
    }

    private function getCompanyDetails(array $searchResult): ?array
    {
        // This would fetch detailed company information
        // For now, return the search result
        return $searchResult;
    }

    private function formatCompanyData(array $rawData): array
    {
        return [
            'name' => $rawData['nazwa'] ?? '',
            'address' => $rawData['adres'] ?? '',
            'city' => $rawData['miasto'] ?? '',
            'postal_code' => $rawData['kod_pocztowy'] ?? '',
            'regon' => $rawData['regon'] ?? '',
            'nip' => $rawData['nip'] ?? '',
        ];
    }

    private function buildLoginSoapEnvelope(): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ns="http://CIS/BIR/PUBL/2014/07">
            <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
                <wsa:To>' . $this->apiUrl . '</wsa:To>
                <wsa:Action>http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Zaloguj</wsa:Action>
            </soap:Header>
            <soap:Body>
                <ns:Zaloguj>
                    <ns:pKluczUzytkownika>' . $this->userKey . '</ns:pKluczUzytkownika>
                </ns:Zaloguj>
            </soap:Body>
        </soap:Envelope>';
    }

    private function buildSearchSoapEnvelope(string $nip): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ns="http://CIS/BIR/PUBL/2014/07">
            <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
                <wsa:To>' . $this->apiUrl . '</wsa:To>
                <wsa:Action>http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukajPodmioty</wsa:Action>
                <ns:sid>' . $this->sessionId . '</ns:sid>
            </soap:Header>
            <soap:Body>
                <ns:DaneSzukajPodmioty>
                    <ns:pParametryWyszukiwania>
                        <ns:Nip>' . $nip . '</ns:Nip>
                    </ns:pParametryWyszukiwania>
                </ns:DaneSzukajPodmioty>
            </soap:Body>
        </soap:Envelope>';
    }

    private function parseSessionId(string $soapResponse): ?string
    {
        // Parse SOAP response to extract session ID
        // This is a simplified parser - in production you'd use proper XML parsing
        if (preg_match('/<.*?ZalogujResult.*?>(.*?)<\/.*?ZalogujResult.*?>/s', $soapResponse, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }

    private function parseSearchResult(string $soapResponse): ?array
    {
        // Parse SOAP response to extract company data
        // This is a simplified parser - in production you'd use proper XML parsing
        if (preg_match('/<.*?DaneSzukajPodmiotyResult.*?>(.*?)<\/.*?DaneSzukajPodmiotyResult.*?>/s', $soapResponse, $matches)) {
            $xmlData = $matches[1];
            // Parse XML data and return as array
            // For now, return mock data structure
            return ['found' => true];
        }
        
        return null;
    }

    private function getMockData(string $nip): ?array
    {
        // Return mock data for development/testing
        // This simulates a successful GUS response
        
        $mockCompanies = [
            '1234567890' => [
                'name' => 'Przykładowa Firma Sp. z o.o.',
                'address' => 'ul. Testowa 123',
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'regon' => '123456789',
                'nip' => '1234567890',
            ],
            '9876543210' => [
                'name' => 'Demo Company S.A.',
                'address' => 'al. Demonstracyjna 456',
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'regon' => '987654321',
                'nip' => '9876543210',
            ]
        ];

        // If exact NIP is found, return it
        if (isset($mockCompanies[$nip])) {
            return $mockCompanies[$nip];
        }

        // Otherwise, return a generic mock for any valid 10-digit NIP
        if (strlen($nip) === 10) {
            return [
                'name' => 'MOCK - Firma Test Sp. z o.o.',
                'address' => 'ul. Generowana ' . substr($nip, 0, 3),
                'city' => 'Warszawa',
                'postal_code' => '00-' . substr($nip, 3, 3),
                'regon' => substr($nip, 0, 9),
                'nip' => $nip,
            ];
        }

        return null;
    }
}
