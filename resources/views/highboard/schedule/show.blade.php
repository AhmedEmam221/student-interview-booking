@extends('highboard.layouts.app')

@section('title', 'Schedule Slot Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Schedule Slot Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('highboard.schedule.edit', $scheduleSlot) }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Edit
            </a>
            <a href="{{ route('highboard.schedule.index') }}" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                Back to Schedule
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm text-gray-500">Track</dt>
                <dd class="mt-1 text-gray-900">{{ $scheduleSlot->track->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Date</dt>
                <dd class="mt-1 text-gray-900">{{ $scheduleSlot->date->format('l, F j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Time</dt>
                <dd class="mt-1 text-gray-900">{{ $scheduleSlot->start_time->format('g:i A') }} - {{ $scheduleSlot->end_time->format('g:i A') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Max Interviews</dt>
                <dd class="mt-1 text-gray-900">{{ $scheduleSlot->max_interviews }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Available Slots</dt>
                <dd class="mt-1 text-gray-900">{{ $scheduleSlot->available_slots }} / {{ $scheduleSlot->max_interviews }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $scheduleSlot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $scheduleSlot->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm text-gray-500">Board Members</dt>
                <dd class="mt-1 flex flex-wrap gap-1">
                    @if($scheduleSlot->boardMembers->isEmpty())
                        <span class="text-gray-500">None assigned</span>
                    @else
                        @foreach($scheduleSlot->boardMembers as $member)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded">{{ $member->name }}</span>
                        @endforeach
                    @endif
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm text-gray-500">Applications</dt>
                <dd class="mt-1">
                    @if($scheduleSlot->applications->isEmpty())
                        <p class="text-gray-500">No applications for this slot</p>
                    @else
                        <div class="space-y-2">
                            @foreach($scheduleSlot->applications as $application)
                                <a href="{{ route('highboard.applications.show', $application) }}" 
                                   class="block p-3 bg-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-50 border border-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-200 rounded-lg hover:bg-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-100 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-800">{{ $application->full_name }}</div>
                                            <div class="text-xs text-gray-600">{{ $application->track->name }}</div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                                {{ $application->status === 'Accepted' ? 'bg-green-100 text-green-800' : 
                                                   ($application->status === 'Waiting' ? 'bg-blue-100 text-blue-800' : 
                                                   ($application->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ $application->status }}
                                            </span>
                                            @if($application->interviewBoardMember)
                                                <div class="text-xs text-gray-500 mt-1">{{ $application->interviewBoardMember->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection