<?php

namespace Database\Seeders;

use App\Models\ComplaintTopic;
use Illuminate\Database\Seeder;

class ComplaintTopicsSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            [
                'name' => 'Uszkodzona paczka',
                'description' => 'Paczka dotarła uszkodzona lub w złym stanie',
                'default_priority' => 'high',
                'estimated_resolution_hours' => 24,
                'sort_order' => 10,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => true,
            ],
            [
                'name' => 'Zagubiona przesyłka',
                'description' => 'Paczka nie dotarła w przewidywanym terminie',
                'default_priority' => 'urgent',
                'estimated_resolution_hours' => 12,
                'sort_order' => 20,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => false,
            ],
            [
                'name' => 'Nieprawidłowy adres dostawy',
                'description' => 'Paczka została dostarczona na niewłaściwy adres',
                'default_priority' => 'high',
                'estimated_resolution_hours' => 24,
                'sort_order' => 30,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => false,
            ],
            [
                'name' => 'Problem z płatnością',
                'description' => 'Problemy związane z rozliczeniem lub płatnością',
                'default_priority' => 'medium',
                'estimated_resolution_hours' => 48,
                'sort_order' => 40,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => false,
            ],
            [
                'name' => 'Nieudana próba dostawy',
                'description' => 'Kurier nie mógł dostarczyć paczki',
                'default_priority' => 'medium',
                'estimated_resolution_hours' => 24,
                'sort_order' => 50,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => false,
            ],
            [
                'name' => 'Problem z API',
                'description' => 'Problemy techniczne z integracją API',
                'default_priority' => 'high',
                'estimated_resolution_hours' => 12,
                'sort_order' => 60,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => false,
            ],
            [
                'name' => 'Inne',
                'description' => 'Inne problemy nieujęte w powyższych kategoriach',
                'default_priority' => 'low',
                'estimated_resolution_hours' => 72,
                'sort_order' => 100,
                'is_active' => true,
                'customer_visible' => true,
                'requires_attachment' => false,
            ],
        ];

        foreach ($topics as $topic) {
            ComplaintTopic::create($topic);
        }
    }
}
