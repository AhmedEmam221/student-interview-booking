@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-primary-600 px-6 py-8">
            <h1 class="text-3xl font-bold text-white">Hult Prize Interview Registration</h1>
            <p class="mt-2 text-primary-100">Track: {{ $mainTrack->name }}</p>
        </div>
        
        <form action="{{ route('registration.store') }}" method="POST" class="p-6 space-y-8" id="registrationForm">
            @csrf
            
            <!-- Step 1: Personal Information -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="bg-primary-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                    Personal Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Enter your full name">
                        @error('full_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Enter your email">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Enter your phone number">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="major" class="block text-sm font-medium text-gray-700 mb-1">Major <span class="text-red-500">*</span></label>
                        <select id="major" name="major" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="">Select Major</option>
                            @foreach($majors as $major)
                                <option value="{{ $major }}" {{ old('major') == $major ? 'selected' : '' }}>{{ $major }}</option>
                            @endforeach
                        </select>
                        @error('major')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                        <select id="academic_year" name="academic_year" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('academic_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sub-Track <span class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            @if($mainTrack->children->isNotEmpty())
                                <select id="track_id" name="track_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                    onchange="loadTimeSlots()">
                                    <option value="">Select {{ $mainTrack->name }} Sub-track</option>
                                    @foreach($mainTrack->children as $subTrack)
                                        <option value="{{ $subTrack->id }}" {{ old('track_id') == $subTrack->id ? 'selected' : '' }}>
                                            {{ $subTrack->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="track_id" id="track_id" value="{{ $mainTrack->id }}">
                                <p class="text-sm text-gray-500">No sub-tracks available for {{ $mainTrack->name }}. Using main track.</p>
                            @endif
                            @error('track_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Strengths & Weaknesses -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="bg-primary-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                    Strengths & Weaknesses
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Strengths (Select all that apply)</h3>
                        <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                            @foreach($strengths as $strength)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="strengths[]" value="{{ $strength->id }}" 
                                        {{ in_array($strength->id, old('strengths', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 transition-colors">
                                    <span class="ml-3 text-sm text-gray-700">{{ $strength->name }}</span>
                                    @if($strength->description)
                                        <span class="ml-2 text-xs text-gray-400">({{ $strength->description }})</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Weaknesses (Select all that apply)</h3>
                        <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                            @foreach($weaknesses as $weakness)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="weaknesses[]" value="{{ $weakness->id }}" 
                                        {{ in_array($weakness->id, old('weaknesses', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 transition-colors">
                                    <span class="ml-3 text-sm text-gray-700">{{ $weakness->name }}</span>
                                    @if($weakness->description)
                                        <span class="ml-2 text-xs text-gray-400">({{ $weakness->description }})</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Student Activity -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="bg-primary-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                    Student Activity
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Did you join a student activity before? <span class="text-red-500">*</span></label>
                        <div class="flex space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="joined_activity_before" value="1" {{ old('joined_activity_before', false) ? 'checked' : '' }} 
                                    class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500" id="joined_yes">
                                <span class="ml-2 text-sm text-gray-700">Yes</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="joined_activity_before" value="0" {{ !old('joined_activity_before', false) ? 'checked' : '' }} 
                                    class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500" id="joined_no">
                                <span class="ml-2 text-sm text-gray-700">No</span>
                            </label>
                        </div>
                        @error('joined_activity_before')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div id="activityNameField" class="{{ old('joined_activity_before') ? '' : 'hidden' }}">
                        <label for="activity_name" class="block text-sm font-medium text-gray-700 mb-1">Student Activity Name <span class="text-red-500">*</span></label>
                        <input type="text" id="activity_name" name="activity_name" value="{{ old('activity_name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Enter the name of the student activity">
                        @error('activity_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Additional Information -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="bg-primary-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                    Additional Information
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="hobbies" class="block text-sm font-medium text-gray-700 mb-1">Hobbies</label>
                        <textarea id="hobbies" name="hobbies" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Tell us about your hobbies and interests">{{ old('hobbies') }}</textarea>
                    </div>
                    
                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                        <textarea id="comments" name="comments" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Any additional comments or information">{{ old('comments') }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Step 5: Interview Slot Selection -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <span class="bg-primary-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                    Select Interview Slot
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="preferred_date" class="block text-sm font-medium text-gray-700 mb-1">Preferred Date <span class="text-red-500">*</span></label>
                        <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            min="{{ now()->format('Y-m-d') }}"
                            max="2026-10-07">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Available Time Slots <span class="text-red-500">*</span></label>
                        <div id="timeSlotsContainer" class="space-y-3">
                            <p class="text-gray-500 text-sm">Please select a sub-track and date first to see available time slots.</p>
                        </div>
                        <input type="hidden" name="schedule_slot_id" id="schedule_slot_id" required>
                        @error('schedule_slot_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="pt-6 border-t border-gray-200">
                <button type="submit" 
                    class="w-full md:w-auto px-8 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Track and date selection for time slots
    const trackSelect = document.getElementById('track_id');
    const dateInput = document.getElementById('preferred_date');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const slotIdInput = document.getElementById('schedule_slot_id');
    
    async function loadTimeSlots() {
        const trackId = trackSelect ? trackSelect.value : null;
        const date = dateInput.value;
        
        if (!trackId || !date) {
            timeSlotsContainer.innerHTML = '<p class="text-gray-500 text-sm">Please select a sub-track and date first to see available time slots.</p>';
            return;
        }
        
        timeSlotsContainer.innerHTML = '<p class="text-gray-500 text-sm">Loading available slots...</p>';
        
        try {
            const response = await fetch(`{{ route('registration.slots') }}?track_id=${trackId}&date=${date}`);
            const data = await response.json();
            
            if (data.slots.length === 0) {
                timeSlotsContainer.innerHTML = '<p class="text-gray-500 text-sm">No available slots for this date and track.</p>';
                return;
            }
            
            timeSlotsContainer.innerHTML = data.slots.map(slot => `
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-colors">
                    <input type="radio" name="schedule_slot_id" value="${slot.id}" 
                        class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500"
                        ${slotIdInput.value == slot.id ? 'checked' : ''}>
                    <div class="ml-3">
                        <div class="font-medium text-gray-900">${slot.start_time} - ${slot.end_time}</div>
                        <div class="text-sm text-gray-500">${slot.available_count} of ${slot.max_interviews} slots available</div>
                    </div>
                </label>
            `).join('');
            
            // Add event listeners to radio buttons
            document.querySelectorAll('input[name="schedule_slot_id"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    slotIdInput.value = this.value;
                });
            });
        } catch (error) {
            console.error('Error loading slots:', error);
            timeSlotsContainer.innerHTML = '<p class="text-red-500 text-sm">Error loading time slots. Please try again.</p>';
        }
    }
    
    if (trackSelect) {
        trackSelect.addEventListener('change', loadTimeSlots);
    }
    dateInput.addEventListener('change', loadTimeSlots);
    
    // Show/hide activity name field
    const joinedYes = document.getElementById('joined_yes');
    const joinedNo = document.getElementById('joined_no');
    const activityNameField = document.getElementById('activityNameField');
    
    function toggleActivityField() {
        if (joinedYes.checked) {
            activityNameField.classList.remove('hidden');
        } else {
            activityNameField.classList.add('hidden');
            document.getElementById('activity_name').value = '';
        }
    }
    
    joinedYes.addEventListener('change', toggleActivityField);
    joinedNo.addEventListener('change', toggleActivityField);
    
    // Initialize on page load
    toggleActivityField();
</script>
@endpush