<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeysController extends Controller
{
    public function index()
    {
        $keys = ApiKey::orderByDesc('created_at')->paginate(20);
        return view('admin.settings.api', compact('keys'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'scope' => 'sometimes|string',
            'label' => 'sometimes|string|max:255',
        ]);

        $scope = $request->input('scope', 'map.read');
        $label = $request->input('label');

        // Generate unique key with prefix
        do {
            $key = 'map_' . Str::random(40);
        } while (ApiKey::where('key', $key)->exists());

        $apiKey = ApiKey::create([
            'key' => $key,
            'name' => $label,
            'scopes' => [$scope],
            'status' => 'active',
        ]);

        return redirect()->route('admin.settings.api')->with('generated_key', $apiKey->key);
    }

    public function revoke(ApiKey $apiKey)
    {
        $apiKey->update(['status' => 'revoked']);
        return back()->with('success', 'Klucz został unieważniony.');
    }
}

