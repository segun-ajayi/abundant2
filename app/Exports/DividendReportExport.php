<?php

namespace App\Exports;

use App\Models\Dividend;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DividendReportExport implements FromCollection, WithCustomStartCell, WithHeadings, WithMapping,
    WithStrictNullComparison, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    private Dividend $dividend;
    private int $index = 0;

    public function __construct(Dividend $dividend)
    {
        $this->dividend = $dividend;
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'E' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'F' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'G' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'H' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'I' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'J' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'K' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'L' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
            'M' => NumberFormat::FORMAT_CURRENCY_NGN_INTEGER,
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->dividend->reports;
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function map($member): array
    {
        $this->index += 1;
        return [
            $this->index,
            $member->member->name,
            $member->amount,
            $member->status ? 'PAID' : 'UNPAID',
            Carbon::parse($member->pDate)->format('d M, Y')
        ];
    }

    public function headings(): array
    {
        $year = $this->dividend->year;
        return [
            ['TOTAL DIVIDEND', $this->dividend->amount],
            ['TOTAL SHARED', $this->dividend->shared],
            ['EXCESS', $this->dividend->excess],
            [Str::upper("YEAR $year DIVIDEND REPORT")],
            ['#', 'NAME', 'AMOUNT', 'STATUS', 'DATE'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK
                ],
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK
                ]
            ],
            'font' => ['bold' => true, 'size' => 14]
        ];
        $styleArray2 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK
                ]
            ],
            'font' => ['size' => 14]
        ];
        $sheet->mergeCells('B5:F5')->getStyle('B5:F5')
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:C4')
            ->applyFromArray($styleArray);
        $sheet->getStyle('B5:F5')
            ->applyFromArray($styleArray);
        $sheet->getStyle('B6:F6')
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6:F6')
            ->applyFromArray($styleArray);
        $sheet->getStyle('B7:F' . $this->dividend->reports->count() + 6)
            ->applyFromArray($styleArray2);

        $styleArray3 = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK
                ],
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK
                ]
            ],
            'font' => ['bold' => true, 'size' => 14]
        ];

        $row = $this->dividend->reports->count() + 7;

        $sheet->setCellValue('B' . $row, 'TOTAL');
        $sheet->mergeCells("B$row:C$row")->getStyle("B$row:C$row")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('D' . $row, $this->dividend->reports->sum('amount'));

        $sheet->getStyle("B$row:D$row")
            ->applyFromArray($styleArray3);

        $sheet->getStyle("B$row:I$row")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_CURRENCY_NGN_INTEGER);

        $sheet->getStyle("B2:C4")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_CURRENCY_NGN_INTEGER);
    }
}
