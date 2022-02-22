<?php

namespace App\Exports;

use App\Models\Dividend;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DividendExport implements FromQuery, WithCustomStartCell, WithHeadings, WithMapping
{

    private $year;
    private $total;
    private $shared;
    private $paid;
    private $unpaid;
    private $excess;

    public function __construct($year, $total, $shared, $paid, $unpaid, $excess) {
        $this->year = $year;
        $this->total = $total;
        $this->shared = $shared;
        $this->paid = $paid;
        $this->unpaid = $unpaid;
        $this->excess = $excess;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return Dividend::where('year', $this->year);
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function map($dividend): array
    {
        return [
            $dividend->member->member_id,
            $dividend->member->name,
            $dividend->amount,
            $dividend->status === 'unpaid' ? '' : $dividend->mode,
            $dividend->status,
        ];
    }

    public function headings(): array
    {
        return [
            ['Total Dividend', $this->total],
            ['Total Shared', $this->shared],
            ['Total Shared', $this->excess],
            ['Total Shared', $this->paid],
            ['Total Shared', $this->unpaid],
            ['Member ID', 'Name', 'Amount', 'Mode', 'Status'],
        ];
    }
}
