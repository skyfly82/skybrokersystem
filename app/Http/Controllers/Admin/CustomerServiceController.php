<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerComplaint;
use App\Models\ComplaintTopic;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerServiceController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'open_complaints' => CustomerComplaint::where('status', 'open')->count(),
            'in_progress' => CustomerComplaint::where('status', 'in_progress')->count(),
            'waiting_customer' => CustomerComplaint::where('status', 'waiting_customer')->count(),
            'resolved_today' => CustomerComplaint::where('status', 'resolved')
                                               ->whereDate('resolved_at', today())
                                               ->count(),
        ];

        $recent_complaints = CustomerComplaint::with(['customer', 'customerUser', 'topic', 'assignedTo'])
                                             ->latest()
                                             ->limit(10)
                                             ->get();

        $urgent_complaints = CustomerComplaint::with(['customer', 'customerUser', 'topic'])
                                             ->where('priority', 'urgent')
                                             ->whereIn('status', ['open', 'in_progress'])
                                             ->latest()
                                             ->limit(5)
                                             ->get();

        return view('admin.customer-service.dashboard', compact('stats', 'recent_complaints', 'urgent_complaints'));
    }

    public function complaints(): View
    {
        $query = CustomerComplaint::with(['customer', 'customerUser', 'topic', 'assignedTo']);

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('priority')) {
            $query->where('priority', request('priority'));
        }

        if (request('topic')) {
            $query->where('complaint_topic_id', request('topic'));
        }

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('complaint_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $complaints = $query->orderBy('created_at', 'desc')->paginate(25);
        $topics = ComplaintTopic::active()->ordered()->get();
        $agents = SystemUser::whereIn('role', ['admin', 'super_admin'])->orderBy('name')->get();

        return view('admin.customer-service.complaints.index', compact('complaints', 'topics', 'agents'));
    }

    public function showComplaint(CustomerComplaint $complaint): View
    {
        $complaint->load([
            'customer',
            'customerUser',
            'topic',
            'assignedTo',
            'resolvedBy',
            'messages.sender',
            'files'
        ]);

        $agents = SystemUser::whereIn('role', ['admin', 'super_admin'])->orderBy('name')->get();

        return view('admin.customer-service.complaints.show', compact('complaint', 'agents'));
    }

    public function assignComplaint(Request $request, CustomerComplaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:system_users,id']
        ]);

        $complaint->assignTo((int) $validated['assigned_to']);

        return redirect()
            ->route('admin.customer-service.complaints.show', $complaint)
            ->with('success', 'Reklamacja została przypisana.');
    }

    public function updateStatus(Request $request, CustomerComplaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,waiting_customer,resolved,closed']
        ]);

        $complaint->update(['status' => $validated['status']]);

        return redirect()
            ->route('admin.customer-service.complaints.show', $complaint)
            ->with('success', 'Status reklamacji został zaktualizowany.');
    }

    public function resolveComplaint(Request $request, CustomerComplaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'resolution' => ['required', 'string', 'max:5000']
        ]);

        $complaint->markAsResolved($validated['resolution'], auth('system_user')->id());

        return redirect()
            ->route('admin.customer-service.complaints.show', $complaint)
            ->with('success', 'Reklamacja została rozwiązana.');
    }

    public function addMessage(Request $request, CustomerComplaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'is_internal' => ['boolean']
        ]);

        $validated['is_internal'] = $request->has('is_internal');

        $complaint->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => auth('system_user')->id(),
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal']
        ]);

        if (!$validated['is_internal'] && $complaint->status === 'waiting_customer') {
            $complaint->update(['status' => 'in_progress']);
        }

        return redirect()
            ->route('admin.customer-service.complaints.show', $complaint)
            ->with('success', 'Wiadomość została dodana.');
    }
}
