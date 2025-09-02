<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ComplaintTopic;
use App\Models\CustomerComplaint;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(): View
    {
        $user = auth('customer_user')->user();

        $complaints = CustomerComplaint::with(['topic', 'shipment', 'assignedTo'])
            ->where('customer_id', $user->customer_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('customer.complaints.index', compact('complaints'));
    }

    public function create(Request $request): View
    {
        $user = auth('customer_user')->user();

        // Get customer's shipments for complaint selection
        $shipments = Shipment::where('customer_id', $user->customer_id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Get active complaint topics
        $topics = ComplaintTopic::active()->ordered()->get();

        // Pre-select shipment if provided
        $selectedShipment = null;
        if ($request->filled('shipment')) {
            $selectedShipment = $shipments->where('id', $request->shipment)->first();
        }

        return view('customer.complaints.create', compact(
            'shipments',
            'topics',
            'selectedShipment'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth('customer_user')->user();

        $validated = $request->validate([
            'shipment_id' => ['nullable', 'exists:shipments,id'],
            'complaint_topic_id' => ['required', 'exists:complaint_topics,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'preferred_contact_method' => ['required', 'string', 'in:email,phone,both'],
        ]);

        // Verify shipment belongs to customer
        if ($validated['shipment_id']) {
            $shipment = Shipment::where('id', $validated['shipment_id'])
                ->where('customer_id', $user->customer_id)
                ->first();
            if (! $shipment) {
                return back()->withErrors(['shipment_id' => 'Invalid shipment selected']);
            }
        }

        $complaint = CustomerComplaint::create(array_merge($validated, [
            'customer_id' => $user->customer_id,
            'customer_user_id' => $user->id,
            'contact_email' => $validated['contact_email'] ?: $user->email,
        ]));

        // TODO: Integrate with Freshdesk API to create ticket
        // TODO: Send notification to customer service team

        return redirect()
            ->route('customer.complaints.show', $complaint)
            ->with('success', 'Your complaint has been submitted successfully. Reference number: '.$complaint->complaint_number);
    }

    public function show(CustomerComplaint $complaint): View
    {
        $user = auth('customer_user')->user();

        // Ensure user can view this complaint
        if ($complaint->customer_id !== $user->customer_id) {
            abort(404);
        }

        $complaint->load([
            'topic',
            'shipment',
            'assignedTo',
            'messages.sender:id,name', // Assuming polymorphic relationship
            'files',
        ]);

        return view('customer.complaints.show', compact('complaint'));
    }

    public function addMessage(Request $request, CustomerComplaint $complaint): RedirectResponse
    {
        $user = auth('customer_user')->user();

        // Ensure user can add message to this complaint
        if ($complaint->customer_id !== $user->customer_id) {
            abort(404);
        }

        if ($complaint->isResolved()) {
            return back()->withErrors(['message' => 'Cannot add message to resolved complaint']);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $complaint->messages()->create([
            'sender_type' => 'customer',
            'sender_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Update complaint status if it was waiting for customer
        if ($complaint->status === 'waiting_customer') {
            $complaint->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Your message has been added.');
    }

    public function uploadFile(Request $request, CustomerComplaint $complaint): RedirectResponse
    {
        $user = auth('customer_user')->user();

        // Ensure user can upload to this complaint
        if ($complaint->customer_id !== $user->customer_id) {
            abort(404);
        }

        if ($complaint->isResolved()) {
            return back()->withErrors(['file' => 'Cannot upload files to resolved complaint']);
        }

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png,gif'],
        ]);

        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('complaints/'.$complaint->id, $filename, 'private');

        $complaint->files()->create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'uploaded_by' => $user->id,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }
}
