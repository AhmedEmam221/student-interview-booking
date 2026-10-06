@extends('highboard.layouts.app')

@section('title', 'Board Member Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $boardMember->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('highboard.board-members.edit', $boardMember) }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Edit
            </a>
            <a href="{{ route('highboard.board-members.index') }}" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                Back to List
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Information</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-500">Email</dt>
                    <dd class="text-gray-900">{{ $boardMember->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Phone</dt>
                    <dd class="text-gray-900">{{ $boardMember->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Referral Code</dt>
                    <dd class="text-gray-900 font-mono">{{ $boardMember->referral_code }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Referral Link</dt>
                    <dd class="text-gray-900">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded break-all">{{ url('/register?ref=' . $boardMember->referral_code) }}</code>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Bio</dt>
                    <dd class="text-gray-900">{{ $boardMember->bio ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Tracks</dt>
                    <dd class="text-gray-900">
                        @if($boardMember->tracks->isEmpty())
                            —
                        @else
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($boardMember->tracks as $track)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded">{{ $track->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Status</dt>
                    <dd class="text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $boardMember->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $boardMember->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div class="p-4 bg-orange-50 rounded-lg">
                    <p class="text-3xl font-bold text-orange-600">{{ $boardMember->interview_count }}</p>
                    <p class="text-sm text-orange-700">Total Interviews</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-3xl font-bold text-green-600">{{ $boardMember->referral_count }}</p>
                    <p class="text-sm text-green-700">Total Referrals</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Interviews -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Interviews</h3>
            @if($recentInterviews->isEmpty())
                <p class="text-gray-500 text-center py-4">No interviews yet</p>
            @else
                <div class="space-y-3">
                    @foreach($recentInterviews as $app)
                        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="font-medium text-gray-900">{{ $app->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $app->track->name }} - {{ $app->scheduleSlot->date->format('M j, Y') }} at {{ $app->scheduleSlot->start_time->format('g:i A') }}</div>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium 
                                {{ $app->status === 'Accepted' ? 'bg-green-100 text-green-800' : 
                                   ($app->status === 'Waiting' ? 'bg-blue-100 text-blue-800' : 
                                   ($app->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }} 
                                rounded-full">
                                {{ $app->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Recent Referrals -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Referrals</h3>
            @if($recentReferrals->isEmpty())
                <p class="text-gray-500 text-center py-4">No referrals yet</p>
            @else
                <div class="space-y-3">
                    @foreach($recentReferrals as $app)
                        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="font-medium text-gray-900">{{ $app->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $app->track->name }} - {{ $app->scheduleSlot->date->format('M j, Y') }}</div>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium 
                                {{ $app->status === 'Accepted' ? 'bg-green-100 text-green-800' : 
                                   ($app->status === 'Waiting' ? 'bg-blue-100 text-blue-800' : 
                                   ($app->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }} 
                                rounded-full">
                                {{ $app->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection