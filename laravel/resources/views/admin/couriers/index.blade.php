@extends('layouts.admin')

@section('title', 'Couriers')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Courier Services
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage courier integrations and settings
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- InPost -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">IP</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">InPost</dt>
                            <dd class="text-lg font-medium text-gray-900">Connected</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-500">Paczkomaty & Kurier</div>
                </div>
            </div>
        </div>

        <!-- DHL -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">DHL</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">DHL</dt>
                            <dd class="text-lg font-medium text-gray-500">Not Connected</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-500">Express Delivery</div>
                </div>
            </div>
        </div>

        <!-- DPD -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-700 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">DPD</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">DPD</dt>
                            <dd class="text-lg font-medium text-gray-500">Not Connected</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-500">Parcel Delivery</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
