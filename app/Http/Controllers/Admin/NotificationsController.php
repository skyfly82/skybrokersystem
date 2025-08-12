<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $query = Notification::with(['notifiable'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->channel, function ($query, $channel) {
                return $query->where('channel', $channel);
            });

        $notifications = $query->latest()->paginate(25);

        $stats = [
            'total_notifications' => Notification::count(),
            'sent_notifications' => Notification::where('status', 'sent')->count(),
            'failed_notifications' => Notification::where('status', 'failed')->count(),
            'pending_notifications' => Notification::where('status', 'pending')->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    public function templates()
    {
        $templates = NotificationTemplate::paginate(20);

        return view('admin.notifications.templates', compact('templates'));
    }

    public function createTemplate()
    {
        return view('admin.notifications.create-template');
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:notification_templates,name',
            'type' => 'required|in:email,sms',
            'subject' => 'required_if:type,email|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        NotificationTemplate::create($request->all());

        return redirect()->route('admin.notifications.templates')
            ->with('success', 'Szablon powiadomienia został utworzony.');
    }

    public function editTemplate(NotificationTemplate $template)
    {
        return view('admin.notifications.edit-template', compact('template'));
    }

    public function updateTemplate(Request $request, NotificationTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|unique:notification_templates,name,' . $template->id,
            'type' => 'required|in:email,sms',
            'subject' => 'required_if:type,email|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        $template->update($request->all());

        return redirect()->route('admin.notifications.templates')
            ->with('success', 'Szablon powiadomienia został zaktualizowany.');
    }

    public function testNotification(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:notification_templates,id',
            'recipient_email' => 'required_if:channel,email|email',
            'recipient_phone' => 'required_if:channel,sms|string',
            'channel' => 'required|in:email,sms',
            'variables' => 'nullable|array'
        ]);

        try {
            $this->notificationService->sendTestNotification(
                $request->template_id,
                $request->channel,
                $request->recipient_email ?? $request->recipient_phone,
                $request->variables ?? []
            );

            return back()->with('success', 'Powiadomienie testowe zostało wysłane.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}