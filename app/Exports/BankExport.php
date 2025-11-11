<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Bank Code',
            'Bank Name',
            'Description',
            'Status',
            'Created By',
            'Created Date',
            'Updated By',
            'Updated Date',
        ];
    }

    public function map($bank): array
    {
        return [
            $bank->bank_code ?? '',
            $bank->bank_name ?? '',
            $bank->bank_desc ?? '',
            $bank->rec_status == '1' ? 'Active' : 'Inactive',
            $bank->rec_usercreated ?? '',
            $bank->rec_datecreated ? date('Y-m-d H:i:s', strtotime($bank->rec_datecreated)) : '',
            $bank->rec_userupdate ?? '',
            $bank->rec_dateupdate ? date('Y-m-d H:i:s', strtotime($bank->rec_dateupdate)) : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E2EFDA']]],
        ];
    }

    public function title(): string
    {
        return 'Bank Master';
    }
}
