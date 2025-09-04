<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CourierPoint;
use App\Models\CourierService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCourierPoints extends Command
{
    protected $signature = 'points:import {path : CSV file path} {--courier=} {--type=} {--delimiter=;} {--header}';

    protected $description = 'Import courier pickup/locker points from CSV file';

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        $courierArg = (string) $this->option('courier');
        $type = (string) $this->option('type');
        $delimiter = (string) $this->option('delimiter');
        $hasHeader = (bool) $this->option('header');

        if (! file_exists($path)) {
            $this->error('File not found: '.$path);

            return self::FAILURE;
        }

        // Resolve courier_service_id (accept id or code)
        $courierServiceId = null;
        if (is_numeric($courierArg)) {
            $courierServiceId = (int) $courierArg;
        } else {
            $courierServiceId = CourierService::where('code', $courierArg)->value('id');
        }

        if (! $courierServiceId) {
            $this->error('Invalid courier (use id or code).');

            return self::FAILURE;
        }

        $count = 0;
        if (($handle = fopen($path, 'r')) !== false) {
            $columns = [];
            if ($hasHeader) {
                $columns = fgetcsv($handle, 0, $delimiter) ?: [];
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $data = $this->mapRow($row, $columns);
                if (! $data) {
                    continue;
                }

                // Basic validation
                if (! isset($data['code'], $data['latitude'], $data['longitude'])) {
                    continue;
                }

                CourierPoint::updateOrCreate(
                    [
                        'courier_service_id' => $courierServiceId,
                        'code' => (string) $data['code'],
                    ],
                    [
                        'uuid' => Str::uuid(),
                        'type' => $type ?: ($data['type'] ?? 'pickup_point'),
                        'name' => $data['name'] ?? $data['code'],
                        'description' => $data['description'] ?? null,
                        'street' => $data['street'] ?? null,
                        'building_number' => $data['building_number'] ?? null,
                        'apartment_number' => $data['apartment_number'] ?? null,
                        'city' => $data['city'] ?? null,
                        'postal_code' => $data['postal_code'] ?? null,
                        'country_code' => strtoupper($data['country'] ?? 'PL'),
                        'latitude' => (float) $data['latitude'],
                        'longitude' => (float) $data['longitude'],
                        'opening_hours' => $this->jsonOrNull($data['opening_hours'] ?? null),
                        'functions' => $this->jsonOrNull($data['functions'] ?? null),
                        'is_active' => true,
                        'metadata' => $this->jsonOrNull($data['metadata'] ?? null),
                        'external_id' => $data['external_id'] ?? null,
                    ]
                );

                $count++;
            }
            fclose($handle);
        }

        $this->info("Imported/updated {$count} points for courier_service_id={$courierServiceId}.");

        return self::SUCCESS;
    }

    private function mapRow(array $row, array $columns): ?array
    {
        if (empty($columns)) {
            // Assume fixed column order if no header provided
            // code,name,street,building_number,city,postal_code,country,latitude,longitude
            return [
                'code' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'street' => $row[2] ?? null,
                'building_number' => $row[3] ?? null,
                'city' => $row[4] ?? null,
                'postal_code' => $row[5] ?? null,
                'country' => $row[6] ?? null,
                'latitude' => $row[7] ?? null,
                'longitude' => $row[8] ?? null,
            ];
        }

        $data = [];
        foreach ($columns as $i => $name) {
            if (! array_key_exists($i, $row)) {
                continue;
            }
            $key = strtolower(trim((string) $name));
            $data[$key] = $row[$i];
        }

        return $data;
    }

    private function jsonOrNull($value)
    {
        if (! $value) {
            return null;
        }
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
