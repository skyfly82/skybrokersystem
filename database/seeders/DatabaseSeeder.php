<?php

/**
 * Cel: Główny seeder bazy danych z danymi testowymi
 * Moduł: Database
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            TestAccountSeeder::class,
            ComplaintTopicsSeeder::class,
            CourierPointsSeeder::class,
        ]);
    }
}
