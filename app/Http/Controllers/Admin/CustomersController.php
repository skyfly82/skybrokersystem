<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Notifications\CustomerApproved;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['primaryUser'])
            ->when($request->search, function ($query, $search) {
                return $query->where('company_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            });

        $customers = $query->latest()->paginate(20);

        $statusCounts = [
            'all' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'pending' => Customer::where('status', 'pending')->count(),
            'suspended' => Customer::where('status', 'suspended')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'statusCounts'));
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'users', 
            'shipments' => function ($query) {
                $query->latest()->limit(10);
            },
            'payments' => function ($query) {
                $query->latest()->limit(10);
            },
            'transactions' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        $stats = [
            'total_shipments' => $customer->shipments()->count(),
            'month_shipments' => $customer->getMonthlyShipmentsCount(),
            'total_users' => $customer->users()->count(),
            'active_users' => $customer->users()->where('is_active', true)->count(),
            'total_spent' => $customer->payments()->where('status', 'completed')->sum('amount'),
            'current_balance' => $customer->current_balance,
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Klient został utworzony pomyślnie.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Dane klienta zostały zaktualizowane.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->shipments()->exists()) {
            return back()->withErrors(['error' => 'Nie można usunąć klienta z istniejącymi przesyłkami.']);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Klient został usunięty.');
    }

    public function approve(Customer $customer)
    {
        $customer->update([
            'status' => 'active',
            'verified_at' => now()
        ]);

        // Send notification to customer
        if ($customer->primaryUser()) {
            $customer->primaryUser()->notify(new CustomerApproved($customer));
        }

        return back()->with('success', 'Klient został zatwierdzony.');
    }

    public function suspend(Customer $customer)
    {
        $customer->update(['status' => 'suspended']);

        return back()->with('success', 'Klient został zawieszony.');
    }

    public function regenerateApiKey(Customer $customer)
    {
        $customer->update([
            'api_key' => 'sk_' . \Str::random(48)
        ]);

        return back()->with('success', 'Klucz API został wygenerowany ponownie.');
    }

    public function addBalance(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255'
        ]);

        $customer->addBalance(
            $request->amount, 
            $request->description ?? 'Manual balance adjustment by admin'
        );

        return back()->with('success', "Dodano {$request->amount} PLN do salda klienta.");
    }
}