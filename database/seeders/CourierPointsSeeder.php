<?php

namespace Database\Seeders;

use App\Models\CourierPoint;
use App\Models\CourierService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourierPointsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure InPost courier exists
        $inpost = CourierService::firstOrCreate(
            ['code' => 'inpost'],
            [
                'name' => 'InPost',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $points = [
            [
                'code' => 'WAW01234', 'name' => 'Paczkomat Warszawa WAW01234', 'city' => 'Warszawa',
                'postal_code' => '00-175', 'street' => 'al. Jana Pawła II', 'building_number' => '23',
                'lat' => 52.24234, 'lng' => 20.99612,
            ],
            [
                'code' => 'WAW04567', 'name' => 'Paczkomat Warszawa WAW04567', 'city' => 'Warszawa',
                'postal_code' => '02-676', 'street' => 'Marynarska', 'building_number' => '15',
                'lat' => 52.17991, 'lng' => 20.99656,
            ],
            [
                'code' => 'WAW07890', 'name' => 'Paczkomat Warszawa WAW07890', 'city' => 'Warszawa',
                'postal_code' => '01-001', 'street' => 'Aleje Jerozolimskie', 'building_number' => '54',
                'lat' => 52.22205, 'lng' => 21.00223,
            ],
            [
                'code' => 'KRK01234', 'name' => 'Paczkomat Kraków KRK01234', 'city' => 'Kraków',
                'postal_code' => '31-154', 'street' => 'Długa', 'building_number' => '72',
                'lat' => 50.07068, 'lng' => 19.94008,
            ],
            [
                'code' => 'GDA01234', 'name' => 'Paczkomat Gdańsk GDA01234', 'city' => 'Gdańsk',
                'postal_code' => '80-809', 'street' => 'Kartuska', 'building_number' => '240',
                'lat' => 54.34121, 'lng' => 18.59442,
            ],
        ];

        foreach ($points as $p) {
            CourierPoint::updateOrCreate(
                [
                    'courier_service_id' => $inpost->id,
                    'code' => $p['code'],
                ],
                [
                    'uuid' => Str::uuid(),
                    'type' => 'parcel_locker',
                    'name' => $p['name'],
                    'description' => null,
                    'street' => $p['street'],
                    'building_number' => $p['building_number'],
                    'apartment_number' => null,
                    'city' => $p['city'],
                    'postal_code' => $p['postal_code'],
                    'country_code' => 'PL',
                    'latitude' => $p['lat'],
                    'longitude' => $p['lng'],
                    'opening_hours' => null,
                    'functions' => ['parcel_locker', 'returns', 'payment'],
                    'is_active' => true,
                    'metadata' => ['seed' => true],
                ]
            );
        }
    }
}

