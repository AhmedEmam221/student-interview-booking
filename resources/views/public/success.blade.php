@extends('layouts.public')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-green-600 px-6 py-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-3xl text-green-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Application Submitted Successfully!</h1>
            <p class="mt-2 text-green-100">Your interview has been scheduled</p>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Application Details</h2>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Application Number:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->application_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Name:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->full_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Track:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->track->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Date:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->scheduleSlot->date->format('l, F j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Time:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->scheduleSlot->start_time->format('g:i A') }} - {{ $application->scheduleSlot->end_time->format('g:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Interviewer:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->interviewBoardMember->name }}</dd>
                    </div>
                    @if($application->referralBoardMember)
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Referred by:</dt>
                        <dd class="font-medium text-gray-900">{{ $application->referralBoardMember->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                <h3 class="font-medium text-primary-800 mb-2">Important Information</h3>
                <ul class="text-sm text-primary-700 space-y-1">
                    <li><i class="fas fa-check-circle mr-2"></i>Please arrive 10 minutes before your scheduled time</li>
                    <li><i class="fas fa-check-circle mr-2"></i>Your application number is: <strong>{{ $application->application_number }}</strong></li>
                    <li><i class="fas fa-check-circle mr-2"></i>If you need to reschedule, please reach out to the recruitment booth</li>
                </ul>
            </div>
            
            <div class="text-center pt-4">
                <a href="" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Submit Another Application
                </a>
            </div>
        </div>
    </div>
</div>
@endsection