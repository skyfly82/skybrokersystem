@extends('layouts.admin')

@section('title', 'Create Notification Template')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Create Notification Template
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Create a new email or SMS template for system notifications
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.notifications.templates') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Templates
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.notifications.templates.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template Configuration</h3>
                <p class="mt-1 text-sm text-gray-500">Configure the basic template settings</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Template Name</label>
                        <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Order Confirmation">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Template Type</label>
                        <select name="type" id="template-type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="email">Email Template</option>
                            <option value="sms">SMS Template</option>
                            <option value="push">Push Notification</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6" id="email-fields">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Email Subject</label>
                            <input type="text" name="subject" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Your order has been confirmed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Name</label>
                            <input type="text" name="from_name" value="Sky Broker System" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Email</label>
                            <input type="email" name="from_email" value="noreply@skybrokersystem.com" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mt-6" id="sms-fields" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SMS From Number</label>
                        <input type="text" name="sms_from" value="+48123456789" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template Content</h3>
                <p class="mt-1 text-sm text-gray-500">Create the template content with variables</p>
            </div>
            <div class="p-6">
                <div id="email-content">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Content (HTML)</label>
                    <textarea name="content" rows="12" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your HTML email template here..."><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{subject}}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb;">{{title}}</h1>
        <p>Dear {{customer_name}},</p>
        <p>{{message}}</p>
        <div style="background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Order Details:</strong><br>
            Order Number: {{order_number}}<br>
            Tracking Number: {{tracking_number}}<br>
            Status: {{status}}
        </div>
        <p>Thank you for choosing Sky Broker System.</p>
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">
        <p style="font-size: 12px; color: #6b7280;">
            This is an automated message from Sky Broker System.<br>
            If you have any questions, please contact our support team.
        </p>
    </div>
</body>
</html></textarea>
                </div>

                <div id="sms-content" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Content</label>
                    <textarea name="sms_content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your SMS template here (max 160 chars)">Hello {{customer_name}}, your order {{order_number}} status: {{status}}. Track: {{tracking_url}}</textarea>
                    <p class="mt-1 text-xs text-gray-500">SMS templates should be concise (max 160 characters recommended)</p>
                </div>

                <div id="push-content" style="display: none;">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Push Title</label>
                            <input type="text" name="push_title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Order Update">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Push Message</label>
                            <textarea name="push_content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter push notification content">Your order {{order_number}} has been {{status}}. {{tracking_info}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Available Variables</h3>
                <p class="mt-1 text-sm text-gray-500">Use these variables in your template content</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gray-50 p-3 rounded-md">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Customer Variables</h4>
                        <div class="text-xs space-y-1 text-gray-600">
                            <code>{{customer_name}}</code><br>
                            <code>{{customer_email}}</code><br>
                            <code>{{customer_phone}}</code><br>
                            <code>{{company_name}}</code>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-md">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Order Variables</h4>
                        <div class="text-xs space-y-1 text-gray-600">
                            <code>{{order_number}}</code><br>
                            <code>{{tracking_number}}</code><br>
                            <code>{{status}}</code><br>
                            <code>{{courier_name}}</code>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-md">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">System Variables</h4>
                        <div class="text-xs space-y-1 text-gray-600">
                            <code>{{date}}</code><br>
                            <code>{{time}}</code><br>
                            <code>{{tracking_url}}</code><br>
                            <code>{{support_email}}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template Settings</h3>
                <p class="mt-1 text-sm text-gray-500">Additional template configuration</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Activate template immediately
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="send_test" id="send_test" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="send_test" class="ml-2 block text-sm text-gray-900">
                            Send test notification after creating template
                        </label>
                    </div>
                </div>

                <div class="mt-4" id="test-recipient" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700">Test Recipient</label>
                    <input type="text" name="test_recipient" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Email or phone number for testing">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.notifications.templates') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Create Template
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateType = document.getElementById('template-type');
    const emailFields = document.getElementById('email-fields');
    const smsFields = document.getElementById('sms-fields');
    const emailContent = document.getElementById('email-content');
    const smsContent = document.getElementById('sms-content');
    const pushContent = document.getElementById('push-content');
    const sendTest = document.getElementById('send_test');
    const testRecipient = document.getElementById('test-recipient');

    function toggleFields() {
        const type = templateType.value;
        
        // Hide all fields
        emailFields.style.display = 'none';
        smsFields.style.display = 'none';
        emailContent.style.display = 'none';
        smsContent.style.display = 'none';
        pushContent.style.display = 'none';
        
        // Show relevant fields
        if (type === 'email') {
            emailFields.style.display = 'block';
            emailContent.style.display = 'block';
        } else if (type === 'sms') {
            smsFields.style.display = 'block';
            smsContent.style.display = 'block';
        } else if (type === 'push') {
            pushContent.style.display = 'block';
        }
    }

    templateType.addEventListener('change', toggleFields);
    
    sendTest.addEventListener('change', function() {
        testRecipient.style.display = this.checked ? 'block' : 'none';
    });

    // Initialize
    toggleFields();
});
</script>
@endsection