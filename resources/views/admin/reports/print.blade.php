<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Sheet - {{ now()->format('Y-m-d') }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 10px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #333; }
        .header p { margin: 5px 0; font-size: 12px; color: #666; }
        .filters { margin-bottom: 20px; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; vertical-align: top; }
        th { background-color: #f5f5f5; font-weight: bold; position: sticky; top: 0; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-no-show { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded">Print / Save as PDF</button>
        <a href="{{ route('admin.reports.index', request()->query()) }}" class="px-4 py-2 bg-gray-600 text-white rounded ml-2">Back</a>
    </div>
    
    <div class="header">
        <h1>Interview Schedule Sheet</h1>
        <p>Generated on {{ now()->format('l, F j, Y \a\t g:i A') }}</p>
        <p>Total Applications: {{ $applications->count() }}</p>
    </div>
    
    <div class="filters">
        <strong>Filters Applied:</strong>
        @if(request('track_id'))
            Track: {{ \App\Models\Track::find(request('track_id'))->name ?? 'Unknown' }} |
        @endif
        @if(request('date_from'))
            From: {{ request('date_from') }} |
        @endif
        @if(request('date_to'))
            To: {{ request('date_to') }} |
        @endif
        @if(request('status'))
            Status: {{ request('status') }}
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>App #</th>
                <th>Applicant</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Major</th>
                <th>Year</th>
                <th>Track</th>
                <th>Date</th>
                <th>Time</th>
                <th>Interviewer</th>
                <th>Referral</th>
                <th>Strengths</th>
                <th>Weaknesses</th>
                <th>Activity</th>
                <th>Hobbies</th>
                <th>Comments</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
                <tr>
                    <td>{{ $application->application_number }}</td>
                    <td>{{ $application->full_name }}</td>
                    <td>{{ $application->phone }}</td>
                    <td>{{ $application->email }}</td>
                    <td>{{ $application->major }}</td>
                    <td>{{ $application->academic_year }}</td>
                    <td>{{ $application->track->name }}</td>
                    <td>{{ $application->scheduleSlot ? $application->scheduleSlot->date->format('Y-m-d') : '' }}</td>
                    <td>{{ $application->scheduleSlot ? $application->scheduleSlot->start_time->format('H:i') . ' - ' . $application->scheduleSlot->end_time->format('H:i') : '' }}</td>
                    <td>{{ $application->interviewBoardMember->name ?? '' }}</td>
                    <td>{{ $application->referralBoardMember->name ?? '' }}</td>
                    <td>{{ $application->strengths->pluck('name')->implode(', ') }}</td>
                    <td>{{ $application->weaknesses->pluck('name')->implode(', ') }}</td>
                    <td>{{ $application->joined_activity_before ? ($application->activity_name ?? 'Yes') : 'No' }}</td>
                    <td>{{ $application->hobbies ?? '' }}</td>
                    <td>{{ $application->comments ?? '' }}</td>
                    <td>
                        <span class="status status-{{ strtolower(str_replace(' ', '-', $application->status)) }}">
                            {{ $application->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>