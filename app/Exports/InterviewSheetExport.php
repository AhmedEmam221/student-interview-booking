<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InterviewSheetExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'Application Number',
            'Applicant',
            'Phone',
            'Email',
            'Major',
            'Academic Year',
            'Track',
            'Date',
            'Time',
            'Interview Board Member',
            'Referral Board Member',
            'Strengths',
            'Weaknesses',
            'Student Activity',
            'Hobbies',
            'Comments',
            'Status',
        ];
    }

    public function map($application): array
    {
        return [
            $application->application_number,
            $application->full_name,
            $application->phone,
            $application->email,
            $application->major,
            $application->academic_year,
            $application->track->name ?? '',
            $application->scheduleSlot ? $application->scheduleSlot->date->format('Y-m-d') : '',
            $application->scheduleSlot ? $application->scheduleSlot->start_time->format('H:i') . ' - ' . $application->scheduleSlot->end_time->format('H:i') : '',
            $application->interviewBoardMember->name ?? '',
            $application->referralBoardMember->name ?? '',
            $application->strengths->pluck('name')->implode(', '),
            $application->weaknesses->pluck('name')->implode(', '),
            $application->joined_activity_before ? ($application->activity_name ?? 'Yes') : 'No',
            $application->hobbies ?? '',
            $application->comments ?? '',
            $application->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}