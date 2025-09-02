<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.system', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required|string',
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();

                if ($setting) {
                    // Convert value based on type
                    $convertedValue = match ($setting->type) {
                        'integer' => (int) $value,
                        'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                        'json' => json_decode($value, true) ?? $value,
                        default => $value
                    };

                    SystemSetting::set($key, $convertedValue, $setting->type, $setting->group, $setting->description);
                }
            }

            // Clear all settings cache
            Cache::flush();

            return back()->with('success', 'Ustawienia systemowe zostały zaktualizowane pomyślnie.');

        } catch (\Exception $e) {
            return back()->with('error', 'Wystąpił błąd podczas aktualizacji ustawień: '.$e->getMessage());
        }
    }

    public function verification()
    {
        $verificationSettings = SystemSetting::where('group', 'verification')
            ->orderBy('key')
            ->get();

        return view('admin.settings.verification', compact('verificationSettings'));
    }

    public function updateVerification(Request $request)
    {
        $request->validate([
            'verification_code_expiry_minutes' => 'required|integer|min:1|max:1440', // 1 min to 24 hours
            'verification_link_expiry_hours' => 'required|integer|min:1|max:168', // 1 hour to 7 days
            'auto_cleanup_unverified_accounts_hours' => 'required|integer|min:1|max:8760', // 1 hour to 1 year
        ]);

        try {
            SystemSetting::set(
                'verification_code_expiry_minutes',
                $request->verification_code_expiry_minutes,
                'integer',
                'verification',
                'Czas ważności kodu weryfikacyjnego w minutach'
            );

            SystemSetting::set(
                'verification_link_expiry_hours',
                $request->verification_link_expiry_hours,
                'integer',
                'verification',
                'Czas ważności linku weryfikacyjnego w godzinach'
            );

            SystemSetting::set(
                'auto_cleanup_unverified_accounts_hours',
                $request->auto_cleanup_unverified_accounts_hours,
                'integer',
                'verification',
                'Automatyczne usuwanie niezweryfikowanych kont po godzinach'
            );

            // Clear cache
            Cache::tags(['system_settings'])->flush();

            return back()->with('success', 'Ustawienia weryfikacji zostały zaktualizowane pomyślnie.');

        } catch (\Exception $e) {
            return back()->with('error', 'Wystąpił błąd podczas aktualizacji ustawień: '.$e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Mail::raw('To jest email testowy wysłany z panelu administratora SkyBrokerSystem.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email - SkyBrokerSystem');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email testowy został wysłany na adres: '.$request->test_email,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Błąd wysyłania emaila: '.$e->getMessage(),
            ], 500);
        }
    }
}
