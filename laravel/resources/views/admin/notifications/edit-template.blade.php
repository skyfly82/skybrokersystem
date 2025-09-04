@extends('layouts.admin')

@section('title', 'Edit Notification Template')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Template: {{ $template->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Modify the {{ $template->type }} template settings and content
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Send Test
            </button>
            <a href="{{ route('admin.notifications.templates') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Templates
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.notifications.templates.update', $template->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template Configuration</h3>
                <p class="mt-1 text-sm text-gray-500">Configure the basic template settings</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Template Name</label>
                        <input type="text" name="name" value="{{ $template->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Template Type</label>
                        <select name="type" id="template-type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="email" {{ $template->type === 'email' ? 'selected' : '' }}>Email Template</option>
                            <option value="sms" {{ $template->type === 'sms' ? 'selected' : '' }}>SMS Template</option>
                            <option value="push" {{ $template->type === 'push' ? 'selected' : '' }}>Push Notification</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6" id="email-fields" {{ $template->type !== 'email' ? 'style=display:none' : '' }}>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Email Subject</label>
                            <input type="text" name="subject" value="{{ $template->subject }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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

                <div class="mt-6" id="sms-fields" {{ $template->type !== 'sms' ? 'style=display:none' : '' }}>
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
                <p class="mt-1 text-sm text-gray-500">Edit the template content with variables</p>
            </div>
            <div class="p-6">
                <div id="email-content" {{ $template->type !== 'email' ? 'style=display:none' : '' }}>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Content (HTML)</label>
                    <textarea name="content" rows="12" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ $template->content ?? '<!DOCTYPE html>
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
</html>' }}</textarea>
                </div>

                <div id="sms-content" {{ $template->type !== 'sms' ? 'style=display:none' : '' }}>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Content</label>
                    <textarea name="sms_content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">Hello {{customer_name}}, your order {{order_number}} status: {{status}}. Track: {{tracking_url}}</textarea>
                    <p class="mt-1 text-xs text-gray-500">SMS templates should be concise (max 160 characters recommended)</p>
                </div>

                <div id="push-content" {{ $template->type !== 'push' ? 'style=display:none' : '' }}>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Push Title</label>
                            <input type="text" name="push_title" value="Order Update" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Push Message</label>
                            <textarea name="push_content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">Your order {{order_number}} has been {{status}}. {{tracking_info}}</textarea>
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
                <p class="mt-1 text-sm text-gray-500">Template status and testing options</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Template Status</h4>
                            <p class="text-sm text-gray-500">Enable or disable this template</p>
                        </div>
                        <button type="button" class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 bg-blue-600" role="switch" aria-checked="true">
                            <span class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Usage Statistics</h4>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">1,234</div>
                                <div class="text-xs text-gray-500">Times Sent</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">98.5%</div>
                                <div class="text-xs text-gray-500">Delivery Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">15.2%</div>
                                <div class="text-xs text-gray-500">Open Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600">3.1%</div>
                                <div class="text-xs text-gray-500">Click Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Template Preview</h3>
                <p class="mt-1 text-sm text-gray-500">Preview how your template will look</p>
            </div>
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Template Preview</h3>
                        <p class="mt-1 text-sm text-gray-500">Click "Generate Preview" to see how your template will appear</p>
                        <div class="mt-6">
                            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Generate Preview
                            </button>
                        </div>
                    </div>
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
                Update Template
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
    
    // Initialize
    toggleFields();
});
</script>
@endsection