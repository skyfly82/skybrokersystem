<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintTopic;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ComplaintTopicController extends Controller
{
    public function index(): View
    {
        $topics = ComplaintTopic::withCount('complaints')
                                ->orderBy('sort_order')
                                ->paginate(20);

        return view('admin.customer-service.topics.index', compact('topics'));
    }

    public function create(): View
    {
        return view('admin.customer-service.topics.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:complaint_topics,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'default_priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'auto_assign_to' => ['nullable', 'exists:system_users,id'],
            'estimated_resolution_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'requires_attachment' => ['boolean'],
            'customer_visible' => ['boolean']
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['requires_attachment'] = $request->has('requires_attachment');
        $validated['customer_visible'] = $request->has('customer_visible');

        if (empty($validated['sort_order'])) {
            $lastTopic = ComplaintTopic::orderBy('sort_order', 'desc')->first();
            $validated['sort_order'] = $lastTopic ? $lastTopic->sort_order + 10 : 10;
        }

        ComplaintTopic::create($validated);

        return redirect()
            ->route('admin.customer-service.topics.index')
            ->with('success', 'Temat reklamacji został utworzony.');
    }

    public function show(ComplaintTopic $topic): View
    {
        $topic->load(['complaints' => function ($query) {
            $query->with(['customer', 'customerUser'])
                  ->latest()
                  ->limit(10);
        }]);

        return view('admin.customer-service.topics.show', compact('topic'));
    }

    public function edit(ComplaintTopic $topic): View
    {
        return view('admin.customer-service.topics.edit', compact('topic'));
    }

    public function update(Request $request, ComplaintTopic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:complaint_topics,name,' . $topic->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'default_priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'auto_assign_to' => ['nullable', 'exists:system_users,id'],
            'estimated_resolution_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'requires_attachment' => ['boolean'],
            'customer_visible' => ['boolean']
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['requires_attachment'] = $request->has('requires_attachment');
        $validated['customer_visible'] = $request->has('customer_visible');

        $topic->update($validated);

        return redirect()
            ->route('admin.customer-service.topics.index')
            ->with('success', 'Temat reklamacji został zaktualizowany.');
    }

    public function destroy(ComplaintTopic $topic): RedirectResponse
    {
        if ($topic->complaints()->count() > 0) {
            return redirect()
                ->route('admin.customer-service.topics.index')
                ->with('error', 'Nie można usunąć tematu, który ma przypisane reklamacje.');
        }

        $topic->delete();

        return redirect()
            ->route('admin.customer-service.topics.index')
            ->with('success', 'Temat reklamacji został usunięty.');
    }

    public function toggle(ComplaintTopic $topic): RedirectResponse
    {
        $topic->update(['is_active' => !$topic->is_active]);

        $status = $topic->is_active ? 'aktywowany' : 'dezaktywowany';
        
        return redirect()
            ->route('admin.customer-service.topics.index')
            ->with('success', "Temat reklamacji został {$status}.");
    }
}
