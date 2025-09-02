<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsNotificationBanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CmsNotificationBannerController extends Controller
{
    public function index(): View
    {
        $banners = CmsNotificationBanner::with('creator')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.cms.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.cms.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,warning,error,success'],
            'position' => ['required', 'string', 'in:top,bottom'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'display_rules' => ['nullable', 'array'],
            'display_rules.pages' => ['nullable', 'array'],
            'display_rules.exclude_pages' => ['nullable', 'array'],
        ]);

        $banner = CmsNotificationBanner::create($validated);

        return redirect()
            ->route('admin.cms.banners.show', $banner)
            ->with('success', 'Banner created successfully.');
    }

    public function show(CmsNotificationBanner $banner): View
    {
        $banner->load('creator');

        return view('admin.cms.banners.show', compact('banner'));
    }

    public function edit(CmsNotificationBanner $banner): View
    {
        return view('admin.cms.banners.edit', compact('banner'));
    }

    public function update(Request $request, CmsNotificationBanner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'string', 'in:info,warning,error,success'],
            'position' => ['required', 'string', 'in:top,bottom'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'display_rules' => ['nullable', 'array'],
            'display_rules.pages' => ['nullable', 'array'],
            'display_rules.exclude_pages' => ['nullable', 'array'],
        ]);

        $banner->update($validated);

        return redirect()
            ->route('admin.cms.banners.show', $banner)
            ->with('success', 'Banner updated successfully.');
    }

    public function destroy(CmsNotificationBanner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()
            ->route('admin.cms.banners.index')
            ->with('success', 'Banner deleted successfully.');
    }

    public function toggle(CmsNotificationBanner $banner): JsonResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $banner->is_active,
            'message' => $banner->is_active ? 'Banner activated' : 'Banner deactivated',
        ]);
    }
}
