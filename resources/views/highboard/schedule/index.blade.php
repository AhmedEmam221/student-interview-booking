@extends('highboard.layouts.app')

@section('title', 'Schedule Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Weekly Schedule</h1>
    <div class="flex space-x-3">
        <a href="{{ route('highboard.schedule.create') }}" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add Slot
        </a>
        <button onclick="openBulkCreateModal()" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-calendar-plus mr-2"></i>
            Bulk Create
        </button>
    </div>
</div>

<!-- Week Navigation -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex items-center space-x-4">
        <div>
            <label for="week_start" class="block text-sm font-medium text-gray-700 mb-1">Week Starting</label>
            <input type="date" id="week_start" name="week_start" value="{{ $weekStart }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
        </div>
        
        <div>
            <label for="track_id" class="block text-sm font-medium text-gray-700 mb-1">Track</label>
            <select id="track_id" name="track_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                <option value="">All Tracks</option>
                @foreach($tracks as $track)
                    <option value="{{ $track->id }}" {{ request('track_id') == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-end space-x-3">
            <button type="submit" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                View Week
            </button>
            <a href="{{ route('highboard.schedule.index', ['week_start' => now()->startOfWeek()->format('Y-m-d')]) }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                This Week
            </a>
        </div>
    </form>
</div>

<!-- Schedule Grid -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px]">
            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Time</th>
                    @foreach($weekDates as $date)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l border-gray-200 min-w-[140px]">
                            <div class="font-medium text-gray-900">{{ $date->format('D') }}</div>
                            <div class="text-sm text-gray-600">{{ $date->format('M j') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $time)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-700 w-32 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                        </td>
                        @foreach($weekDates as $date)
                            <td class="px-2 py-2 border-l border-gray-200 align-top" style="min-height: 80px;">
                                @php
                                    $dateKey = $date->format('Y-m-d');
                                    $slotsForTime = $schedule[$dateKey] ?? collect();
                                    $slot = $slotsForTime->firstWhere('start_time', $time);
                                @endphp
                                
                                @if($slot)
                                    @if($slot->applications->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($slot->applications as $application)
                                                <a href="{{ route('highboard.applications.show', $application) }}" 
                                                   class="block p-2 bg-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-50 border border-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-200 rounded-lg hover:bg-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-100 transition-colors">
                                                    <div class="font-medium text-{{ $application->status === 'Accepted' ? 'green' : ($application->status === 'Waiting' ? 'blue' : ($application->status === 'Pending' ? 'yellow' : 'red')) }}-800 text-sm">{{ $application->full_name }}</div>
                                                    <div class="text-xs text-gray-600">{{ $application->track->name }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                                            {{ $application->status === 'Accepted' ? 'bg-green-100 text-green-800' : 
                                                               ($application->status === 'Waiting' ? 'bg-blue-100 text-blue-800' : 
                                                               ($application->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                            {{ $application->status }}
                                                        </span>
                                                        @if($application->interviewBoardMember)
                                                            <span class="ml-1">{{ $application->interviewBoardMember->name }}</span>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="p-2 bg-orange-50 border border-orange-200 rounded-lg cursor-pointer hover:bg-orange-100 transition-colors"
                                            onclick="openSlotModal({{ $slot->id }})">
                                            <div class="font-medium text-orange-800 text-sm">{{ $slot->track->name }}</div>
                                            <div class="text-xs text-orange-600">
                                                {{ $slot->available_slots }}/{{ $slot->max_interviews }} slots available
                                            </div>
                                            <div class="text-xs text-orange-500 mt-1">
                                                {{ $slot->boardMembers->pluck('name')->implode(', ') }}
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="p-2 text-center text-gray-300 text-xs">
                                        —
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Slot Modal -->
<div id="slotModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Slot Details</h3>
            <button onclick="closeSlotModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="modalContent">
            Loading...
        </div>
    </div>
</div>

<!-- Bulk Create Modal -->
<div id="bulkCreateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Bulk Create Schedule</h3>
            <button onclick="closeBulkCreateModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="bulkCreateForm" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label for="bulk_week_start" class="block text-sm font-medium text-gray-700 mb-1">Week Starting <span class="text-red-500">*</span></label>
                <input type="date" id="bulk_week_start" name="week_start" value="{{ now()->startOfWeek()->format('Y-m-d') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
            </div>
            
            <div>
                <label for="bulk_track_id" class="block text-sm font-medium text-gray-700 mb-1">Track <span class="text-red-500">*</span></label>
                <select id="bulk_track_id" name="track_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <option value="">Select Track</option>
                    @foreach($tracks as $track)
                        <option value="{{ $track->id }}">{{ $track->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Days <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-7 gap-2">
                    @php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    @endphp
                    @foreach($days as $index => $day)
                        <label class="flex items-center justify-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="days[]" value="{{ $day }}" class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" {{ in_array($day, ['monday','tuesday','wednesday','thursday','friday']) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">{{ $dayLabels[$index] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div id="bulkTimeSlots">
                <label class="block text-sm font-medium text-gray-700 mb-2">Time Slots <span class="text-red-500">*</span></label>
                <div class="space-y-3" id="timeSlotEntries">
                    <!-- Time slot entries will be added here -->
                </div>
                <button type="button" onclick="addTimeSlotEntry()" class="mt-3 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Time Slot
                </button>
            </div>
            
            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeBulkCreateModal()" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    Create Schedule
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let timeSlotCounter = 0;
    
    // Store all board members with their track IDs
    const allBoardMembers = @json($boardMembers->map(function($m) {
        return [
            'id' => $m->id,
            'name' => $m->name,
            'track_ids' => $m->tracks->pluck('id')->toArray()
        ];
    }));
    
    // Store all tracks for sub-track detection
    const allTracks = @json($tracks->map(function($t) {
        return [
            'id' => $t->id,
            'name' => $t->name,
            'parent_id' => $t->parent_id
        ];
    }));
    
    function openSlotModal(slotId) {
        const modal = document.getElementById('slotModal');
        const content = document.getElementById('modalContent');
        content.innerHTML = 'Loading...';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        fetch(`{{ route('highboard.schedule.show', ':id') }}`.replace(':id', slotId))
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = '<p class="text-red-500">Error loading slot details</p>';
            });
    }
    
    function closeSlotModal() {
        document.getElementById('slotModal').classList.add('hidden');
        document.getElementById('slotModal').classList.remove('flex');
    }
    
    function openBulkCreateModal() {
        document.getElementById('bulkCreateModal').classList.remove('hidden');
        document.getElementById('bulkCreateModal').classList.add('flex');
        // Add first time slot entry
        if (document.getElementById('timeSlotEntries').children.length === 0) {
            addTimeSlotEntry();
        }
    }
    
    function closeBulkCreateModal() {
        document.getElementById('bulkCreateModal').classList.add('hidden');
        document.getElementById('bulkCreateModal').classList.remove('flex');
    }
    
    // Filter board members by track
    function getBoardMembersForTrack(trackId) {
        if (!trackId) return allBoardMembers;
        
        // Ensure trackId is an integer for comparison
        const trackIdInt = parseInt(trackId, 10);
        if (isNaN(trackIdInt)) return allBoardMembers;
        
        // Find the track to check if it's a main track or sub-track
        const track = allTracks.find(t => t.id === trackIdInt);
        if (!track) return allBoardMembers;
        
        if (track.parent_id === null) {
            // Main track - get all board members for this track AND its sub-tracks
            const subTrackIds = allTracks.filter(t => t.parent_id === trackIdInt).map(t => t.id);
            const allTrackIds = [trackIdInt, ...subTrackIds];
            return allBoardMembers.filter(bm => bm.track_ids.some(tid => allTrackIds.includes(tid)));
        } else {
            // Sub-track - get board members for this specific sub-track
            return allBoardMembers.filter(bm => bm.track_ids.includes(trackIdInt));
        }
    }
    
    // Generate board member options HTML for a track
    function generateBoardMemberOptions(trackId) {
        const members = getBoardMembersForTrack(trackId);
        return members.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
    }
    
    function updateBoardMemberSelects(trackId) {
        const optionsHtml = generateBoardMemberOptions(trackId);
        document.querySelectorAll('#timeSlotEntries select[name$="[board_member_ids][]"]').forEach(select => {
            const currentValue = Array.from(select.selectedOptions).map(o => o.value);
            select.innerHTML = optionsHtml;
            // Restore selection if possible
            currentValue.forEach(val => {
                const option = select.querySelector(`option[value="${val}"]`);
                if (option) option.selected = true;
            });
        });
    }
    
    function addTimeSlotEntry() {
        const container = document.getElementById('timeSlotEntries');
        const entry = document.createElement('div');
        entry.className = 'bg-gray-50 p-4 rounded-lg space-y-3';
        
        const bulkTrackId = document.getElementById('bulk_track_id').value;
        const boardMemberOptions = generateBoardMemberOptions(bulkTrackId);
        
        entry.innerHTML = `
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-gray-900">Time Slot #${++timeSlotCounter}</h4>
                <button type="button" onclick="this.closest('.bg-gray-50').remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <select name="time_slots[${timeSlotCounter}][start_time]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        ${Array.from({length: 15}, (_, i) => {
                            const hour = 9 + Math.floor(i / 2);
                            const minute = (i % 2) * 30;
                            const time = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                            return `<option value="${time}">${time}</option>`;
                        }).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <select name="time_slots[${timeSlotCounter}][end_time]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        ${Array.from({length: 15}, (_, i) => {
                            const hour = 9 + Math.floor(i / 2);
                            const minute = (i % 2) * 30;
                            const time = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                            return `<option value="${time}">${time}</option>`;
                        }).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Interviews</label>
                    <input type="number" name="time_slots[${timeSlotCounter}][max_interviews]" value="1" min="1" max="10" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Board Members</label>
                    <select name="time_slots[${timeSlotCounter}][board_member_ids][]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" size="4">
                        ${boardMemberOptions}
                    </select>
                </div>
            </div>
        `;
        container.appendChild(entry);
    }
    
    // Handle track selection change
    document.getElementById('bulk_track_id').addEventListener('change', function() {
        const trackId = parseInt(this.value, 10);
        const hint = document.getElementById('trackSelectionHint');
        
        if (!isNaN(trackId)) {
            const track = allTracks.find(t => t.id === trackId);
            if (track && track.parent_id === null) {
                hint.textContent = `Main track selected. Board members from this track and its sub-tracks will be available.`;
                hint.classList.remove('hidden');
            } else {
                hint.textContent = `Sub-track selected. Only board members assigned to this sub-track will be available.`;
                hint.classList.remove('hidden');
            }
            updateBoardMemberSelects(trackId);
        } else {
            hint.classList.add('hidden');
            updateBoardMemberSelects(null);
        }
    });
    
    // Close modals on backdrop click
    document.getElementById('slotModal').addEventListener('click', function(e) {
        if (e.target === this) closeSlotModal();
    });
    
    document.getElementById('bulkCreateModal').addEventListener('click', function(e) {
        if (e.target === this) closeBulkCreateModal();
    });
    
    // Copy week functionality
    function copyWeek() {
        const sourceWeek = prompt('Enter source week start date (YYYY-MM-DD):');
        const targetWeek = prompt('Enter target week start date (YYYY-MM-DD):');
        const trackId = prompt('Enter track ID:');
        
        if (sourceWeek && targetWeek && trackId) {
            fetch('{{ route('highboard.schedule.copy-week') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    source_week_start: sourceWeek,
                    target_week_start: targetWeek,
                    track_id: trackId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Week copied successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }
</script>
@endpush