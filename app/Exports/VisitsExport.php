<?php

namespace App\Exports;

use App\Services\ImportExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;
    protected ImportExportService $importExportService;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->importExportService = new ImportExportService();
    }

    /**
     * Get the collection of data to export
     */
    public function collection()
    {
        return $this->importExportService->getVisitsForExport($this->filters);
    }

    /**
     * Map each row of the collection to the format for export
     */
    public function map($visit): array
    {
        return [
            $visit->id,
            $visit->customer?->name ?? 'N/A',
            $visit->customer?->email ?? 'N/A',
            $this->formatMobileNumber($visit->customer),
            $visit->outlet?->name ?? 'N/A',
            $visit->staff?->name ?? 'N/A',
            $visit->bill_amount ?? 0,
            $visit->points_awarded ?? 0,
            $visit->visit_type ?? 'regular',
            $visit->visited_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $visit->notes ?? '',
        ];
    }

    /**
     * Get the headings for the export file
     */
    public function headings(): array
    {
        return [
            'Visit ID',
            'Customer Name',
            'Customer Email',
            'Customer Mobile',
            'Outlet',
            'Staff',
            'Bill Amount',
            'Points Awarded',
            'Visit Type',
            'Visit Date & Time',
            'Notes',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold header
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF'],
                ],
            ],
        ];
    }

    /**
     * Format mobile number from customer
     */
    protected function formatMobileNumber($customer): string
    {
        if (!$customer || !$customer->mobile_json) {
            return 'N/A';
        }

        $mobile = $customer->mobile_json;
        $countryCode = $mobile['country_dial_code'] ?? '';
        $number = $mobile['national_number'] ?? '';
        
        return $countryCode . ' ' . $number;
    }
}

