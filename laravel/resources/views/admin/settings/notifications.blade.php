@extends('layouts.admin')

@section('title', 'Notification Settings')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Notification Settings
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Configure email and SMS notifications for the system
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <button type="submit" form="notifications-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Settings
            </button>
        </div>
    </div>

    <form id="notifications-form" method="POST" action="{{ route('admin.settings.notifications.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Email Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Email Notifications</h3>
                <p class="mt-1 text-sm text-gray-500">Configure email notification settings</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
                        <input type="text" name="smtp_host" value="smtp.gmail.com" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
                        <input type="number" name="smtp_port" value="587" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Email</label>
                        <input type="email" name="from_email" value="noreply@skybrokersystem.com" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Name</label>
                        <input type="text" name="from_name" value="Sky Broker System" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Email Templates</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Order Confirmation</div>
                                <div class="text-sm text-gray-500">Sent when a new shipment is created</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="email_templates[order_confirmation]" checked class="h-4 w-4 text-blue-600">
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-500">Edit Template</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Tracking Update</div>
                                <div class="text-sm text-gray-500">Sent when shipment status changes</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="email_templates[tracking_update]" checked class="h-4 w-4 text-blue-600">
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-500">Edit Template</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Payment Confirmation</div>
                                <div class="text-sm text-gray-500">Sent when payment is processed</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="email_templates[payment_confirmation]" checked class="h-4 w-4 text-blue-600">
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-500">Edit Template</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">SMS Notifications</h3>
                <p class="mt-1 text-sm text-gray-500">Configure SMS notification settings</p>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="sms_enabled" id="sms_enabled" checked class="h-4 w-4 text-blue-600">
                        <label for="sms_enabled" class="ml-2 text-sm font-medium text-gray-900">Enable SMS notifications</label>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SMS Provider</label>
                        <select name="sms_provider" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="twilio" selected>Twilio</option>
                            <option value="smsapi">SMS API</option>
                            <option value="nexmo">Nexmo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Number</label>
                        <input type="text" name="sms_from" value="+48123456789" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">SMS Templates</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Delivery Notification</div>
                                <div class="text-sm text-gray-500">Sent when package is out for delivery</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="sms_templates[delivery_notification]" checked class="h-4 w-4 text-blue-600">
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-500">Edit Template</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Pickup Ready</div>
                                <div class="text-sm text-gray-500">Sent when package is ready for pickup</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="sms_templates[pickup_ready]" class="h-4 w-4 text-blue-600">
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-500">Edit Template</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Push Notifications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Push Notifications</h3>
                <p class="mt-1 text-sm text-gray-500">Configure in-app and browser push notifications</p>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Browser Push Notifications</div>
                            <div class="text-sm text-gray-500">Send notifications to user's browser</div>
                        </div>
                        <input type="checkbox" name="browser_push_enabled" checked class="h-4 w-4 text-blue-600">
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">In-App Notifications</div>
                            <div class="text-sm text-gray-500">Show notifications within the application</div>
                        </div>
                        <input type="checkbox" name="inapp_notifications_enabled" checked class="h-4 w-4 text-blue-600">
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Rules -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Notification Rules</h3>
                <p class="mt-1 text-sm text-gray-500">Configure when notifications are sent</p>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Send notifications for:</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_rules[new_orders]" checked class="h-4 w-4 text-blue-600">
                                <label class="ml-2 text-sm text-gray-700">New orders</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_rules[status_changes]" checked class="h-4 w-4 text-blue-600">
                                <label class="ml-2 text-sm text-gray-700">Status changes</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_rules[payment_updates]" checked class="h-4 w-4 text-blue-600">
                                <label class="ml-2 text-sm text-gray-700">Payment updates</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_rules[delivery_attempts]" class="h-4 w-4 text-blue-600">
                                <label class="ml-2 text-sm text-gray-700">Failed delivery attempts</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="notification_rules[system_alerts]" checked class="h-4 w-4 text-blue-600">
                                <label class="ml-2 text-sm text-gray-700">System alerts</label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quiet Hours</label>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500">From</label>
                                <input type="time" name="quiet_hours_start" value="22:00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">To</label>
                                <input type="time" name="quiet_hours_end" value="08:00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">No notifications will be sent during quiet hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Notifications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Test Notifications</h3>
                <p class="mt-1 text-sm text-gray-500">Send test notifications to verify settings</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Test Email
                    </button>
                    
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Test SMS
                    </button>
                    
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM8.5 17H3l5 5v-5zM12 12l3-3 3 3-3 3-3-3z" />
                        </svg>
                        Test Push
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection