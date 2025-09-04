<div class="overflow-hidden bg-white shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="sticky left-0 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                        Permission / Role
                    </th>
                    @foreach($rolesList as $role => $displayName)
                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                        <div class="flex flex-col items-center space-y-2">
                            <span>{{ $displayName }}</span>
                            <div class="flex space-x-1">
                                <button type="button" onclick="selectAllForRole('{{ $userType }}', '{{ $role }}')" 
                                        class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200" title="Select All">
                                    ✓
                                </button>
                                <button type="button" onclick="clearAllForRole('{{ $userType }}', '{{ $role }}')" 
                                        class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200" title="Clear All">
                                    ✗
                                </button>
                            </div>
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($permissions as $category => $categoryPermissions)
                    <!-- Category Header -->
                    <tr class="bg-gray-100">
                        <td class="sticky left-0 bg-gray-100 px-6 py-3 text-sm font-semibold text-gray-900 capitalize border-r border-gray-300">
                            {{ ucfirst($category) }}
                        </td>
                        @foreach($rolesList as $role => $displayName)
                        <td class="px-3 py-3 text-center">
                            <!-- Category actions could go here -->
                        </td>
                        @endforeach
                    </tr>
                    
                    @foreach($categoryPermissions as $permission)
                    <tr class="hover:bg-gray-50">
                        <td class="sticky left-0 bg-white px-6 py-4 text-sm border-r border-gray-200">
                            <div>
                                <div class="font-medium text-gray-900">{{ $permission['display_name'] }}</div>
                                @if($permission['description'])
                                <div class="text-gray-500 text-xs mt-1">{{ $permission['description'] }}</div>
                                @endif
                            </div>
                        </td>
                        @foreach($rolesList as $role => $displayName)
                        <td class="px-3 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <input type="hidden" name="permissions[{{ $userType }}][{{ $role }}][{{ $permission['id'] }}]" value="0">
                                <input type="checkbox" 
                                       name="permissions[{{ $userType }}][{{ $role }}][{{ $permission['id'] }}]" 
                                       value="1"
                                       {{ in_array($permission['name'], $rolePermissions[$userType][$role] ?? []) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-600 border-gray-300 rounded">
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>