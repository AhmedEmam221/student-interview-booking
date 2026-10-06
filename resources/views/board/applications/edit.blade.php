@extends('board.layouts.app')

@section('title', 'Edit Application')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Application: {{ $application->application_number }}</h1>
        
        <form action="{{ route('board.applications.update', $application) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-board-500 focus:border-board-500 transition-colors">
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $application->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="interview_board_member_id" class="block text-sm font-medium text-gray-700 mb-1">Interview Board Member</label>
                <select id="interview_board_member_id" name="interview_board_member_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-board-500 focus:border-board-500 transition-colors">
                    <option value="">Select Board Member</option>
                    @foreach($slots as $slot)
                        @foreach($slot->boardMembers as $member)
                            <option value="{{ $member->id }}" {{ $application->interview_board_member_id == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="schedule_slot_id" class="block text-sm font-medium text-gray-700 mb-1">Schedule Slot</label>
                <select id="schedule_slot_id" name="schedule_slot_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-board-500 focus:border-board-500 transition-colors">
                    <option value="">Select Slot</option>
                    @foreach($slots as $slot)
                        <option value="{{ $slot->id }}" {{ $application->schedule_slot_id == $slot->id ? 'selected' : '' }}>
                            {{ $slot->date->format('M j, Y') }} - {{ $slot->start_time->format('g:i A') }} ({{ $slot->available_slots }}/{{ $slot->max_interviews }} available)
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('board.applications.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-board-600 text-white font-medium rounded-lg hover:bg-board-700 transition-colors">
                    Update Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection