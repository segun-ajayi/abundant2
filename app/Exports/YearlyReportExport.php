<?php

namespace App\Exports;

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

class YearlyReportExport implements FromCollection, WithCustomStartCell, WithHeadings, WithMapping,
    WithStrictNullComparison, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    private $members;
    private Carbon $start;
    private Carbon $end;
    private int $index = 0;
    private $year;

    public function __construct($year)
    {
        $this->year = $year;
        $this->start = Carbon::parse('12/16/' . $year)->startOfYear();
        $this->end = Carbon::parse('12/16/' . $year)->endOfYear();

        $this->generateReport();
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
        return $this->members;
    }

    private function generateReport()
    {
        $members = Member::all()->sortBy('member_id');

        foreach ($members as $member) {

            if ($member->savings) {
                $savingsC = $member->savings->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                $savingsD = $member->savings->history->whereBetween('date', [$this->start, $this->end])->sum('debit');
            } else {
                $savingsD = 0;
                $savingsC = 0;
            }
            if ($member->share) {
                $shareC = $member->share->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                $shareD = $member->share->history->whereBetween('date', [$this->start, $this->end])->sum('debit');
            } else {
                $shareD = 0;
                $shareC = 0;
            }
            if ($member->specialSavings) {
                $specialC = $member->specialSavings->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                $specialD = $member->specialSavings->history->whereBetween('date', [$this->start, $this->end])->sum('debit');
            } else {
                $specialD = 0;
                $specialC = 0;
            }
            if ($member->building) {
                $buildingC = $member->building->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                $buildingD = $member->building->history->whereBetween('date', [$this->start, $this->end])->sum('debit');
            } else {
                $buildingD = 0;
                $buildingC = 0;
            }
            if (!$member->loans->isEmpty()) {
                $cre = 0;
                $int = 0;
                $appLoan = $member->loans->whereBetween('approved_on', [$this->start, $this->end])->sum('amount');
                if ($appLoan) {
                    $appLoan = $appLoan * 1;
                } else {
                    $appLoan = 0;
                }
                $loan = $member->loans;
                if ($loan->count() > 0) {
                    foreach ($loan as $item) {
                        $cre = $cre + $item->repayments->whereBetween('date', [$this->start, $this->end])->sum('credit');
                        $int = $int + $item->repayments->whereBetween('date', [$this->start, $this->end])->sum('interest');
                    }
                }
            } else {
                $cre = 0;
                $int = 0;
                $appLoan = 0;
            }
            if (!$member->fines->isEmpty()) {
//                dd($member->fines);
                $fines = $member->fines->whereBetween('date', [$this->start, $this->end])->sum('credit');
//                dd($fines);
            } else {
                $fines = 0;
            }
            if (!$member->utilities->isEmpty()) {
                $util = $member->utilities->whereBetween('date', [$this->start, $this->end])->sum('amount');
            } else {
                $util = 0;
            }


            $member->savingsM = $savingsC - $savingsD;
            $member->shareM = $shareC - $shareD;
            $member->specialM = $specialC - $specialD;
            $member->buildingM = $buildingC - $buildingD;
            $member->loanRepay = $cre;
            $member->loanBalance = $member->getLoan();
            $member->interest = $int;
            $member->fines = $fines;
            $member->appLoan = $appLoan;
            $member->util = $util;
            $member->totalSavings = $member->getSaving();
            $member->totalShares = $member->getShare();

            $member->sum = $member->savingsM + $member->shareM +
                $member->fines + $member->util + $member->buildingM + $member->loanRepay + $member->interest;
        }
        $filename = 'backup/year_' . $this->year . '.sqlite';
        if (Storage::disk('backup')->exists($filename)) {
            $old = 'backup/year_' . $this->year . '_old.sqlite';
            if (Storage::disk('backup')->exists($old)) {
                Storage::disk('backup')->delete($old);
                Storage::disk('backup')->move($filename, $old);
            } else {
                Storage::disk('backup')->move($filename, $old);
            }
        }
        Storage::disk('backup')->copy('database.sqlite', $filename);
        $this->members = $members;
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
            $member->name,
            $member->savingsM,
            $member->shareM,
            $member->appLoan,
            $member->loanRepay,
            $member->loanBalance,
            $member->interest,
            $member->util,
            $member->fines,
            $member->buildingM,
            $member->sum,

        ];
    }

    public function headings(): array
    {
        $year = $this->start->format('Y');
        return [
            [Str::upper("YEAR $year REPORT")],
            ['#', 'NAME', 'SAVINGS', 'SHARES', 'APPROVED LOAN', 'LOAN REPAYMENT',
                'LOAN BALANCE', 'LOAN INTEREST', 'UTILITY', 'FINE', 'BUILDING', 'TOTAL'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
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
        $sheet->mergeCells('B2:M2')->getStyle('B2:M2')
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:M2')
            ->applyFromArray($styleArray);
        $sheet->getStyle('B3:M3')
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B3:M3')
            ->applyFromArray($styleArray);
        $sheet->getStyle('B4:M' . $this->members->count() + 3)
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

        $row = $this->members->count() + 4;
        $rowData = $this->members->count() + 3;

//        $totalSavings = $this->members->sum('totalSavings');
//        $totalShares = $this->members->sum('totalShares');
        $appLoan = $this->members->sum('appLoan');
        $loanRepay = $this->members->sum('loanRepay');
        $loanBalance = $this->members->sum('loanBalance');
        $interest = $this->members->sum('interest');
        $util = $this->members->sum('util');
        $fines = $this->members->sum('fines');
        $building = $this->members->sum('buildingM');
        $sum = $this->members->sum('sum');
        $savings = $this->members->sum('savingsM');
        $share = $this->members->sum('shareM');

        $sheet->setCellValue('B' . $row, 'GRAND TOTAL');
        $sheet->mergeCells("B$row:C$row")->getStyle("B$row:C$row")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('D' . $row, $savings);
//        $sheet->setCellValue('E' . $row, $totalSavings);
        $sheet->setCellValue('E' . $row, $share);
//        $sheet->setCellValue('G' . $row, $totalShares);
        $sheet->setCellValue('F' . $row, $appLoan);
        $sheet->setCellValue('G' . $row, $loanRepay);
        $sheet->setCellValue('H' . $row, $loanBalance);
        $sheet->setCellValue('I' . $row, $interest);
        $sheet->setCellValue('J' . $row, $util);
        $sheet->setCellValue('K' . $row, $fines);
        $sheet->setCellValue('L' . $row, $building);
        $sheet->setCellValue('M' . $row, $sum);

        $sheet->getStyle("B$row:M$row")
            ->applyFromArray($styleArray3);

        $sheet->getStyle("B$row:M$row")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_CURRENCY_NGN_INTEGER);
    }
}
