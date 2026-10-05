@extends('admin.layouts.app')

@section('title', 'Applications')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Applications</h1>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                placeholder="Name, email, phone, app #">
        </div>
        
        <div>
            <label for="track_id" class="block text-sm font-medium text-gray-700 mb-1">Track</label>
            <select id="track_id" name="track_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                <option value="">All Tracks</option>
                @foreach($tracks as $track)
                    <option value="{{ $track->id }}" {{ request('track_id') == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
        </div>
        
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
        </div>
        
        <div class="md:col-span-2 lg:col-span-5 flex items-end space-x-3">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.applications.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Applications Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($applications->isEmpty())
        <div class="p-8 text-center">
            <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No applications found</h3>
            <p class="text-gray-500">Try adjusting your filters.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Track</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interviewer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referral</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($applications as $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap font-mono text-sm text-gray-900">{{ $application->application_number }}</td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900">{{ $application->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $application->email }}</div>
                            </td>
                            <td class="px-4 py-4 text-gray-700">{{ $application->track->name }}</td>
                            <td class="px-4 py-4 text-gray-700">
                                @if($application->scheduleSlot)
                                    {{ $application->scheduleSlot->date->format('M j, Y') }}<br>
                                    {{ $application->scheduleSlot->start_time->format('g:i A') }} - {{ $application->scheduleSlot->end_time->format('g:i A') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-700">{{ $application->interviewBoardMember->name ?? '—' }}</td>
                            <td class="px-4 py-4 text-gray-700">{{ $application->referralBoardMember->name ?? '—' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $application->status === 'Completed' ? 'bg-green-100 text-green-800' : 
                                       ($application->status === 'Confirmed' ? 'bg-blue-100 text-blue-800' : 
                                       ($application->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($application->status === 'Cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }} 
                                    rounded-full">
                                    {{ $application->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('admin.applications.show', $application) }}" class="text-primary-600 hover:text-primary-900">View</a>
                                <a href="{{ route('admin.applications.edit', $application) }}" class="text-primary-600 hover:text-primary-900">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection