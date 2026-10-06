@extends('highboard.layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Application #{{ $application->application_number }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('highboard.applications.edit', $application) }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Edit
            </a>
            <a href="{{ route('highboard.applications.index') }}" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                Back to List
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm text-gray-500">Application Number</dt>
                <dd class="font-mono text-lg font-medium text-gray-900">{{ $application->application_number }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $application->status === 'Accepted' ? 'bg-green-100 text-green-800' : 
                           ($application->status === 'Waiting' ? 'bg-blue-100 text-blue-800' : 
                           ($application->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }} 
                        rounded-full">
                        {{ $application->status }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Full Name</dt>
                <dd class="mt-1 text-gray-900">{{ $application->full_name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Email</dt>
                <dd class="mt-1 text-gray-900">{{ $application->email }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Phone</dt>
                <dd class="mt-1 text-gray-900">{{ $application->phone }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Major</dt>
                <dd class="mt-1 text-gray-900">{{ $application->major }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Academic Year</dt>
                <dd class="mt-1 text-gray-900">{{ $application->academic_year }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Track</dt>
                <dd class="mt-1 text-gray-900">{{ $application->track->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Date & Time</dt>
                <dd class="mt-1 text-gray-900">
                    @if($application->scheduleSlot)
                        {{ $application->scheduleSlot->date->format('l, F j, Y') }} at {{ $application->scheduleSlot->start_time->format('g:i A') }} - {{ $application->scheduleSlot->end_time->format('g:i A') }}
                    @else
                        Not scheduled
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Interview Board Member</dt>
                <dd class="mt-1 text-gray-900">{{ $application->interviewBoardMember->name ?? 'Not assigned' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Referral Board Member</dt>
                <dd class="mt-1 text-gray-900">{{ $application->referralBoardMember->name ?? 'None' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Joined Activity Before</dt>
                <dd class="mt-1 text-gray-900">{{ $application->joined_activity_before ? 'Yes' : 'No' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Activity Name</dt>
                <dd class="mt-1 text-gray-900">{{ $application->activity_name ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm text-gray-500">Strengths</dt>
                <dd class="mt-1 flex flex-wrap gap-1">
                    @if($application->strengths->isEmpty())
                        <span class="text-gray-500">None</span>
                    @else
                        @foreach($application->strengths as $strength)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">{{ $strength->name }}</span>
                        @endforeach
                    @endif
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm text-gray-500">Weaknesses</dt>
                <dd class="mt-1 flex flex-wrap gap-1">
                    @if($application->weaknesses->isEmpty())
                        <span class="text-gray-500">None</span>
                    @else
                        @foreach($application->weaknesses as $weakness)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">{{ $weakness->name }}</span>
                        @endforeach
                    @endif
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm text-gray-500">Hobbies</dt>
                <dd class="mt-1 text-gray-900">{{ $application->hobbies ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm text-gray-500">Comments</dt>
                <dd class="mt-1 text-gray-900">{{ $application->comments ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Created At</dt>
                <dd class="mt-1 text-gray-900">{{ $application->created_at->format('M j, Y g:i A') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Updated At</dt>
                <dd class="mt-1 text-gray-900">{{ $application->updated_at->format('M j, Y g:i A') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection