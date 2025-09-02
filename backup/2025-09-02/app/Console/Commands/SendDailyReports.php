<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\SystemUser;
use App\Notifications\DailyReportNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyReports extends Command
{
    protected $signature = 'reports:daily {--date= : Date for the report (Y-m-d format)}';

    protected $description = 'Send daily reports to administrators';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : Carbon::yesterday();

        $this->info("Generating daily report for {$date->format('Y-m-d')}...");

        $reportData = $this->generateReportData($date);
        $admins = SystemUser::where('is_active', true)
            ->whereIn('role', ['super_admin', 'admin'])
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new DailyReportNotification($reportData, $date));
        }

        $this->info("Daily reports sent to {$admins->count()} administrators");

        return self::SUCCESS;
    }

    private function generateReportData(Carbon $date): array
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        return [
            'date' => $date->format('Y-m-d'),
            'shipments' => [
                'total' => Shipment::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
                'delivered' => Shipment::whereBetween('delivered_at', [$startOfDay, $endOfDay])->count(),
                'cancelled' => Shipment::where('status', 'cancelled')
                    ->whereBetween('updated_at', [$startOfDay, $endOfDay])->count(),
            ],
            'payments' => [
                'total' => Payment::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
                'completed' => Payment::where('status', 'completed')
                    ->whereBetween('paid_at', [$startOfDay, $endOfDay])->count(),
                'amount' => Payment::where('status', 'completed')
                    ->whereBetween('paid_at', [$startOfDay, $endOfDay])->sum('amount'),
            ],
            'customers' => [
                'new' => Customer::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
                'approved' => Customer::whereBetween('verified_at', [$startOfDay, $endOfDay])->count(),
            ],
        ];
    }
}
