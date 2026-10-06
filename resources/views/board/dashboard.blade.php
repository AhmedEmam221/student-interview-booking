@extends('board.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-board-100 rounded-lg">
                    <i class="fas fa-file-alt text-2xl text-board-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalApplications }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingApplications }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Accepted</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $acceptedApplications }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-hourglass-half text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Waiting</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $waitingApplications }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rejected</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rejectedApplications }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts/Stats Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Applications per Day -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Applications (Last 7 Days)</h3>
            <div class="space-y-3">
                @foreach($applicationsPerDay as $day)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</span>
                        <span class="font-medium text-gray-900">{{ $day['count'] }}</span>
                    </div>
                @endforeach
                @if($applicationsPerDay->isEmpty())
                    <p class="text-gray-500 text-center py-4">No applications in the last 7 days</p>
                @endif
            </div>
        </div>
        
        <!-- Board Member Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Interviews per Board Member</h3>
            <div class="space-y-3">
                @foreach($boardMembers as $member)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ $member->name }}</span>
                        <span class="font-medium text-gray-900">{{ $member->interview_count }}</span>
                    </div>
                @endforeach
                @if($boardMembers->isEmpty())
                    <p class="text-gray-500 text-center py-4">No board members assigned</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Referrals & Slot Availability -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Referrals per Board Member</h3>
            <div class="space-y-3">
                @foreach($boardMembers as $member)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ $member->name }}</span>
                        <span class="font-medium text-gray-900">{{ $member->referral_count }}</span>
                    </div>
                @endforeach
                @if($boardMembers->isEmpty())
                    <p class="text-gray-500 text-center py-4">No referrals yet</p>
                @endif
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Slot Availability (Next 7 Days)</h3>
                <a href="{{ route('admin.schedule.index') }}" class="text-sm text-board-600 hover:text-board-700">View Schedule</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-green-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $availableSlots }}</p>
                    <p class="text-sm text-green-700">Available Slots</p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $fullSlots }}</p>
                    <p class="text-sm text-red-700">Full Slots</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $availableSlots + $fullSlots }}</p>
                    <p class="text-sm text-blue-700">Total Slots</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection