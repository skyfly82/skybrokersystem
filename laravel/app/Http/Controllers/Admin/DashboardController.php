<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Payment;
use App\Models\Shipment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'pending_customers' => Customer::where('status', 'pending')->count(),
            'total_customer_users' => CustomerUser::count(),
            'total_shipments' => Shipment::count(),
            'today_shipments' => Shipment::whereDate('created_at', today())->count(),
            'this_month_shipments' => Shipment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'pending_shipments' => Shipment::whereIn('status', ['created', 'printed'])->count(),
            'delivered_shipments' => Shipment::where('status', 'delivered')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $recent_customers = Customer::with('primaryUser')
            ->latest()
            ->limit(10)
            ->get();

        $recent_shipments = Shipment::with(['customer', 'customerUser', 'courierService'])
            ->latest()
            ->limit(15)
            ->get();

        $monthly_shipments = Shipment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenue_chart = Payment::where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_customers',
            'recent_shipments',
            'monthly_shipments',
            'revenue_chart'
        ));
    }

    public function stats()
    {
        // AJAX endpoint for live stats
        return response()->json([
            'total_shipments' => Shipment::count(),
            'today_shipments' => Shipment::whereDate('created_at', today())->count(),
            'pending_shipments' => Shipment::whereIn('status', ['created', 'printed'])->count(),
            'delivered_shipments' => Shipment::where('status', 'delivered')->count(),
            'total_customers' => Customer::count(),
        ]);
    }
}
