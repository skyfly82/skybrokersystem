<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function index(): View
    {
        $pages = CmsPage::with(['creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.cms.pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:cms_pages,slug'],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'seo_data' => ['nullable', 'array'],
            'is_published' => ['boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($validated['is_published'] ?? false) {
            $validated['published_at'] = now();
        }

        $page = CmsPage::create($validated);

        return redirect()
            ->route('admin.cms.pages.show', $page)
            ->with('success', 'Page created successfully.');
    }

    public function show(CmsPage $page): View
    {
        $page->load(['creator', 'updater']);

        return view('admin.cms.pages.show', compact('page'));
    }

    public function edit(CmsPage $page): View
    {
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:cms_pages,slug,'.$page->id],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'seo_data' => ['nullable', 'array'],
            'is_published' => ['boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (($validated['is_published'] ?? false) && ! $page->is_published) {
            $validated['published_at'] = now();
        } elseif (! ($validated['is_published'] ?? false)) {
            $validated['published_at'] = null;
        }

        $page->update($validated);

        return redirect()
            ->route('admin.cms.pages.show', $page)
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(CmsPage $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.cms.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function publish(CmsPage $page): RedirectResponse
    {
        $page->publish();

        return back()->with('success', 'Page published successfully.');
    }

    public function unpublish(CmsPage $page): RedirectResponse
    {
        $page->unpublish();

        return back()->with('success', 'Page unpublished successfully.');
    }
}
