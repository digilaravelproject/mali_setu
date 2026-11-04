<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReferralReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Collection $reports) {}
    public function collection(): Collection { return $this->reports; }
    public function headings(): array { return ['Date', 'Volunteer', 'Volunteer Code', 'Referred User', 'User Type', 'Referral Code', 'Location', 'Commission Points', 'Status']; }
    public function map($report): array
    {
        $profile = $report->referrer?->volunteerReferralProfile;
        return [
            $report->created_at?->format('d M Y'), $report->referrer?->name, $report->referrer?->reffral_code,
            $report->referredUser?->name, ucfirst((string) $report->referredUser?->user_type), $report->referral_code_used,
            collect([$profile?->district?->name, $profile?->taluka?->name, $profile?->locality?->name])->filter()->implode(', '),
            $report->commission_points, ucfirst($report->status),
        ];
    }
}
