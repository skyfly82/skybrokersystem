<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsMedia;
use App\Models\CmsNotificationBanner;
use Illuminate\View\View;

class MarketingDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pages' => [
                'total' => CmsPage::count(),
                'published' => CmsPage::published()->count(),
                'drafts' => CmsPage::draft()->count(),
            ],
            'media' => [
                'total' => CmsMedia::count(),
                'images' => CmsMedia::images()->count(),
                'size' => $this->formatBytes(CmsMedia::sum('size')),
            ],
            'banners' => [
                'total' => CmsNotificationBanner::count(),
                'active' => CmsNotificationBanner::active()->count(),
                'scheduled' => CmsNotificationBanner::where('is_active', true)
                             ->where('start_date', '>', now())
                             ->count(),
            ]
        ];

        $recentPages = CmsPage::with('creator')
                             ->orderBy('updated_at', 'desc')
                             ->limit(5)
                             ->get();

        $recentMedia = CmsMedia::with('uploader')
                              ->orderBy('created_at', 'desc')
                              ->limit(6)
                              ->get();

        $activeBanners = CmsNotificationBanner::active()
                                             ->with('creator')
                                             ->ordered()
                                             ->limit(3)
                                             ->get();

        return view('admin.cms.dashboard', compact(
            'stats',
            'recentPages',
            'recentMedia',
            'activeBanners'
        ));
    }

    private function formatBytes(int $size): string
    {
        if ($size === 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor(log($size, 1024));
        
        return round($size / pow(1024, $factor), 2) . ' ' . $units[$factor];
    }
}
