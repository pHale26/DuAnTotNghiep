<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PassengerTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['Nguyễn Văn A', '012345678912', '1990-01-01', 'male', 'adult'],
            ['Nguyễn Thị B', '012345678913', '1992-05-10', 'female', 'adult'],
            ['Nguyễn Văn C', '', '2015-08-20', 'male', 'child'],
        ];
    }

    public function headings(): array
    {
        return [
            'Họ và Tên (Bắt buộc)',
            'CCCD/Passport',
            'Ngày Sinh (YYYY-MM-DD)',
            'Giới tính (male/female/other)',
            'Loại khách (adult/child)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0D6EFD']]],
        ];
    }
}
