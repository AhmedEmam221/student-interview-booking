@extends('admin.layouts.app')

@section('title', 'Reports')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Interview Reports</h1>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
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
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
        </div>
        
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
        </div>
        
        <div>
            <label for="time_from" class="block text-sm font-medium text-gray-700 mb-1">Time From</label>
            <input type="time" id="time_from" name="time_from" value="{{ request('time_from') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
        </div>
        
        <div>
            <label for="time_to" class="block text-sm font-medium text-gray-700 mb-1">Time To</label>
            <input type="time" id="time_to" name="time_to" value="{{ request('time_to') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
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
        
        <div class="flex items-end space-x-3">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Action Buttons -->
<div class="flex space-x-3 mb-6">
    <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <i class="fas fa-print mr-2"></i>
        Print View
    </a>
    <a href="{{ route('admin.reports.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
        <i class="fas fa-file-excel mr-2"></i>
        Export Excel
    </a>
    <a href="{{ route('admin.reports.export-csv', request()->query()) }}" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
        <i class="fas fa-file-csv mr-2"></i>
        Export CSV
    </a>
</div>

<!-- Results Table -->
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
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App #</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Major</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Track</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interviewer</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referral</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Strengths</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weaknesses</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hobbies</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comments</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($applications as $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm font-mono text-gray-900">{{ $application->application_number }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $application->full_name }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->phone }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->email }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->major }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->academic_year }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->track->name }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->scheduleSlot ? $application->scheduleSlot->date->format('Y-m-d') : '' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->scheduleSlot ? $application->scheduleSlot->start_time->format('H:i') . ' - ' . $application->scheduleSlot->end_time->format('H:i') : '' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->interviewBoardMember->name ?? '' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->referralBoardMember->name ?? '' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->strengths->pluck('name')->implode(', ') }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->weaknesses->pluck('name')->implode(', ') }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->joined_activity_before ? ($application->activity_name ?? 'Yes') : 'No' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->hobbies ?? '' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $application->comments ?? '' }}</td>
                            <td class="px-3 py-2 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $application->status === 'Accepted' ? 'bg-green-100 text-green-800' : 
                                       ($application->status === 'Waiting' ? 'bg-blue-100 text-blue-800' : 
                                       ($application->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($application->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }} 
                                    rounded-full">
                                    {{ $application->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection