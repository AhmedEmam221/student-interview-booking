@extends('highboard.layouts.app')

@section('title', 'Edit Schedule Slot')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Schedule Slot</h1>
        
        <form action="{{ route('highboard.schedule.update', $scheduleSlot) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date', $scheduleSlot->date->format('Y-m-d')) }}" required min="{{ now()->format('Y-m-d') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                    <select id="start_time" name="start_time" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        @php
                            $start = \Carbon\Carbon::createFromTime(9, 0, 0);
                            $end = \Carbon\Carbon::createFromTime(15, 30, 0);
                            while ($start <= $end) {
                                echo '<option value="' . $start->format('H:i') . '" ' . (old('start_time', $scheduleSlot->start_time->format('H:i')) == $start->format('H:i') ? 'selected' : '') . '>' . $start->format('g:i A') . '</option>';
                                $start->addMinutes(30);
                            }
                        @endphp
                    </select>
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                    <select id="end_time" name="end_time" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        @php
                            $start = \Carbon\Carbon::createFromTime(9, 30, 0);
                            $end = \Carbon\Carbon::createFromTime(16, 0, 0);
                            while ($start <= $end) {
                                echo '<option value="' . $start->format('H:i') . '" ' . (old('end_time', $scheduleSlot->end_time->format('H:i')) == $start->format('H:i') ? 'selected' : '') . '>' . $start->format('g:i A') . '</option>';
                                $start->addMinutes(30);
                            }
                        @endphp
                    </select>
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="track_id" class="block text-sm font-medium text-gray-700 mb-1">Track <span class="text-red-500">*</span></label>
                <select id="track_id" name="track_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <option value="">Select Track</option>
                    @foreach($tracks as $track)
                        <option value="{{ $track->id }}" {{ old('track_id', $scheduleSlot->track_id) == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                    @endforeach
                </select>
                @error('track_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="max_interviews" class="block text-sm font-medium text-gray-700 mb-1">Max Interviews <span class="text-red-500">*</span></label>
                <input type="number" id="max_interviews" name="max_interviews" value="{{ old('max_interviews', $scheduleSlot->max_interviews) }}" min="1" max="10" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                @error('max_interviews')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Board Members</label>
                <div class="space-y-2">
                    @foreach($boardMembers as $member)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="board_member_ids[]" value="{{ $member->id }}" {{ collect(old('board_member_ids', $scheduleSlot->boardMembers->pluck('id')->toArray()))->contains($member->id) ? 'checked' : '' }}
                                class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $member->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $scheduleSlot->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
            
            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('highboard.schedule.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    Update Schedule Slot
                </button>
            </div>
        </form>
    </div>
</div>
@endsection