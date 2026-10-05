@extends('admin.layouts.app')

@section('title', 'Tracks')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Tracks</h1>
    <a href="{{ route('admin.tracks.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>
        Add Track
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($tracks->isEmpty())
        <div class="p-8 text-center">
            <i class="fas fa-tags text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tracks yet</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first track.</p>
            <a href="{{ route('admin.tracks.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Track
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applications</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub-tracks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($tracks as $track)
                        <tr class="hover:bg-gray-50 {{ $track->parent_id ? 'bg-gray-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($track->parent_id)
                                    <span class="ml-4 inline-block">
                                        <i class="fas fa-angle-right text-gray-400 mr-1"></i>
                                    </span>
                                @endif
                                <div class="font-medium text-gray-900 inline">{{ $track->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-700">{{ $track->parent->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-700 max-w-xs truncate">{{ $track->description ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $track->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $track->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $track->applications_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $track->children_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $track->created_at->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('admin.tracks.edit', $track) }}" class="text-primary-600 hover:text-primary-900">Edit</a>
                                <form action="{{ route('admin.tracks.destroy', $track) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this track?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tracks->links() }}
        </div>
    @endif
</div>
@endsection