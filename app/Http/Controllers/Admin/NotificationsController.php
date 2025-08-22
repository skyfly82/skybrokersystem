<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        // Temporary mock data instead of database
        $notifications = collect([
            [
                'id' => 1,
                'title' => 'New Customer Registration',
                'message' => 'A new customer has registered and awaits approval',
                'type' => 'info',
                'read' => false,
                'created_at' => now()->subMinutes(30),
            ],
            [
                'id' => 2,
                'title' => 'System Update',
                'message' => 'System maintenance completed successfully',
                'type' => 'success', 
                'read' => true,
                'created_at' => now()->subHours(2),
            ]
        ]);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function templates()
    {
        $templates = collect([
            [
                'id' => 1,
                'name' => 'Customer Welcome',
                'subject' => 'Welcome to SkyBroker',
                'type' => 'email',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Shipment Delivered',
                'subject' => 'Your shipment has been delivered',
                'type' => 'sms',
                'status' => 'active',
            ]
        ]);

        return view('admin.notifications.templates', compact('templates'));
    }

    public function createTemplate()
    {
        return view('admin.notifications.create-template');
    }

    public function storeTemplate(Request $request)
    {
        return redirect()->route('admin.notifications.templates')->with('success', 'Template created successfully');
    }

    public function editTemplate($template)
    {
        return view('admin.notifications.edit-template', ['template' => (object)[
            'id' => $template,
            'name' => 'Sample Template',
            'subject' => 'Sample Subject',
            'content' => 'Sample content',
            'type' => 'email',
        ]]);
    }

    public function updateTemplate(Request $request, $template)
    {
        return redirect()->route('admin.notifications.templates')->with('success', 'Template updated successfully');
    }

    public function testNotification(Request $request)
    {
        return back()->with('success', 'Test notification sent successfully');
    }
}
