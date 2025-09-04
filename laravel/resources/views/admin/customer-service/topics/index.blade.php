@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Tematy reklamacji</h1>
        <a href="{{ route('admin.customer-service.topics.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Nowy temat
        </a>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorytet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolejność</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reklamacje</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topics as $topic)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $topic->name }}</div>
                                    @if($topic->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($topic->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $topic->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $topic->is_active ? 'Aktywny' : 'Nieaktywny' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $topic->default_priority == 'urgent' ? 'bg-red-100 text-red-800' : ($topic->default_priority == 'high' ? 'bg-orange-100 text-orange-800' : ($topic->default_priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ ucfirst($topic->default_priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $topic->sort_order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $topic->complaints_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.customer-service.topics.edit', $topic) }}" class="text-indigo-600 hover:text-indigo-900">Edytuj</a>
                                    
                                    <form method="POST" action="{{ route('admin.customer-service.topics.toggle', $topic) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-{{ $topic->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $topic->is_active ? 'yellow' : 'green' }}-900">
                                            {{ $topic->is_active ? 'Dezaktywuj' : 'Aktywuj' }}
                                        </button>
                                    </form>
                                    
                                    @if($topic->complaints_count == 0)
                                        <form method="POST" action="{{ route('admin.customer-service.topics.destroy', $topic) }}" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć ten temat?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Usuń</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <p>Brak tematów reklamacji.</p>
                                        <p class="mt-2">
                                            <a href="{{ route('admin.customer-service.topics.create') }}" class="text-blue-600 hover:text-blue-500">
                                                Utwórz pierwszy temat
                                            </a>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($topics->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $topics->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection