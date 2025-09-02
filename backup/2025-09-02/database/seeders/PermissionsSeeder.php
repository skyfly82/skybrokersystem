<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // System Management
            [
                'name' => 'manage_users',
                'display_name' => 'Manage System Users',
                'category' => 'system',
                'description' => 'Create, edit, delete system users (admins)',
            ],
            [
                'name' => 'manage_employees',
                'display_name' => 'Manage Employees',
                'category' => 'system',
                'description' => 'Create, edit, delete employee accounts',
            ],
            [
                'name' => 'manage_permissions',
                'display_name' => 'Manage Permissions',
                'category' => 'system',
                'description' => 'Configure role permissions',
            ],

            // Customer Management
            [
                'name' => 'manage_customers',
                'display_name' => 'Manage Customers',
                'category' => 'customers',
                'description' => 'Create, edit, delete customer accounts',
            ],
            [
                'name' => 'view_customer_details',
                'display_name' => 'View Customer Details',
                'category' => 'customers',
                'description' => 'View detailed customer information',
            ],
            [
                'name' => 'approve_customers',
                'display_name' => 'Approve Customers',
                'category' => 'customers',
                'description' => 'Approve or reject customer registrations',
            ],

            // Shipments
            [
                'name' => 'manage_shipments',
                'display_name' => 'Manage Shipments',
                'category' => 'shipments',
                'description' => 'Create, edit, delete shipments',
            ],
            [
                'name' => 'view_all_shipments',
                'display_name' => 'View All Shipments',
                'category' => 'shipments',
                'description' => 'View shipments from all customers',
            ],
            [
                'name' => 'cancel_shipments',
                'display_name' => 'Cancel Shipments',
                'category' => 'shipments',
                'description' => 'Cancel shipments in progress',
            ],

            // Payments
            [
                'name' => 'manage_payments',
                'display_name' => 'Manage Payments',
                'category' => 'payments',
                'description' => 'Process, refund, and manage payments',
            ],
            [
                'name' => 'access_financials',
                'display_name' => 'Access Financial Data',
                'category' => 'payments',
                'description' => 'View financial reports and payment data',
            ],
            [
                'name' => 'process_refunds',
                'display_name' => 'Process Refunds',
                'category' => 'payments',
                'description' => 'Issue refunds to customers',
            ],

            // Reports
            [
                'name' => 'view_reports',
                'display_name' => 'View Reports',
                'category' => 'reports',
                'description' => 'Access reporting dashboard',
            ],
            [
                'name' => 'export_reports',
                'display_name' => 'Export Reports',
                'category' => 'reports',
                'description' => 'Export reports to files',
            ],
            [
                'name' => 'view_system_analytics',
                'display_name' => 'View System Analytics',
                'category' => 'reports',
                'description' => 'View detailed system analytics',
            ],

            // Settings
            [
                'name' => 'manage_settings',
                'display_name' => 'Manage Settings',
                'category' => 'settings',
                'description' => 'Configure system settings',
            ],
            [
                'name' => 'manage_notifications',
                'display_name' => 'Manage Notifications',
                'category' => 'settings',
                'description' => 'Configure notification settings',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
