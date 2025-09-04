<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = auth()->user()->customer;

        $stats = [
            'total_shipments' => $customer->shipments()->count(),
            'today_shipments' => $customer->shipments()->whereDate('created_at', today())->count(),
            'this_month_shipments' => $customer->getMonthlyShipmentsCount(),
            'pending_shipments' => $customer->shipments()->whereIn('status', ['created', 'printed', 'dispatched'])->count(),
            'delivered_shipments' => $customer->shipments()->where('status', 'delivered')->count(),
            'current_balance' => $customer->current_balance,
            'credit_limit' => $customer->credit_limit,
            'total_spent' => $customer->payments()->where('status', 'completed')->sum('amount'),
        ];

        $recent_shipments = $customer->shipments()
            ->with(['courierService'])
            ->latest()
            ->limit(10)
            ->get();

        $monthly_shipments = $customer->shipments()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $status_distribution = $customer->shipments()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $recent_payments = $customer->payments()
            ->latest()
            ->limit(5)
            ->get();

        return view('customer.dashboard', compact(
            'stats',
            'recent_shipments',
            'monthly_shipments',
            'status_distribution',
            'recent_payments'
        ));
    }
}
