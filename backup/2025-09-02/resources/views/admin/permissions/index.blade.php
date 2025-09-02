@extends('layouts.admin')

@section('content')
<div class="bg-white">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Role Permissions Management</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Configure permissions for each role in the system. Use the matrix below to grant or revoke specific permissions.
                </p>
            </div>
            <button type="button" onclick="document.getElementById('permissions-form').submit()" 
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                Save Permissions
            </button>
        </div>
    </div>

    <form id="permissions-form" method="POST" action="{{ route('admin.permissions.update') }}" class="p-6">
        @csrf

        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.42a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- System Users Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
                System Users (Admin Panel)
            </h4>
            
            @include('admin.permissions.matrix', ['userType' => 'system_user', 'rolesList' => $roles['system_user'], 'permissions' => $permissions, 'rolePermissions' => $rolePermissions])
        </div>

        <!-- Customer Users Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Customer Users (Client Panel)
            </h4>

            @include('admin.permissions.matrix', ['userType' => 'customer_user', 'rolesList' => $roles['customer_user'], 'permissions' => $permissions, 'rolePermissions' => $rolePermissions])
        </div>

        <!-- Bulk Actions -->
        <div class="border-t border-gray-200 pt-6">
            <div class="flex items-center space-x-4">
                <button type="button" onclick="selectAllPermissions()" 
                        class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                    Select All
                </button>
                <button type="button" onclick="clearAllPermissions()" 
                        class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                    Clear All
                </button>
                <div class="flex-1"></div>
                <p class="text-sm text-gray-500">
                    Changes are saved immediately when you click "Save Permissions"
                </p>
            </div>
        </div>
    </form>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
}

function clearAllPermissions() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
}

// Role-based bulk actions
function selectAllForRole(userType, role) {
    document.querySelectorAll(`input[name^="permissions[${userType}][${role}]"]`).forEach(cb => cb.checked = true);
}

function clearAllForRole(userType, role) {
    document.querySelectorAll(`input[name^="permissions[${userType}][${role}]"]`).forEach(cb => cb.checked = false);
}
</script>
@endsection