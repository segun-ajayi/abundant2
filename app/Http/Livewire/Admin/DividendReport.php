<?php

namespace App\Http\Livewire\Admin;

use App\Exports\DividendExport;
use App\Models\Dividend;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DividendReport extends Component
{
    public $dividends;

    public function mount() {
        $div = DB::table('dividends')->
        select(DB::raw("DISTINCT year"))->get();
        $dividends = array();

        foreach($div as $item) {
            $d = collect();
            $f = Dividend::where('year', $item->year)->get();
            $d->total = $f[0]->total;
            $d->year = $f[0]->year;
            $d->shared = $f->sum('amount');
            $d->excess = $f[0]->total - $f->sum('amount');
            $d->paid = $f->where('status', 'paid')->count();
            $d->unpaid = $f->where('status', 'unpaid')->count();

            array_push($dividends, $d);
        }

        $this->dividends = $dividends;
    }

    public function download($year) {
        $div = Dividend::where('year', $year)->get();
        $total = $div[0]->total;
        $shared = $div->sum('amount');
        $excess = $total - $div->sum('amount');
        $paid = $div->where('status', 'paid')->count();
        $unpaid = $div->where('status', 'unpaid')->count();

        return Excel::download(new DividendExport($year, $total, $shared, $paid, $unpaid, $excess), $year . '_dividend.xlsx');

    }

    public function render()
    {
        return view('livewire.admin.dividend-report');
    }
}
