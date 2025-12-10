<?php

namespace App\Exports;

use App\Models\Production;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProduksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $selectedDate;

    protected $selectedWorker;

    public function __construct($selectedDate, $selectedWorker = 'semua')
    {
        $this->selectedDate = $selectedDate;
        $this->selectedWorker = $selectedWorker;
    }

    public function collection()
    {
        $query = Production::with(['product', 'workers'])
            ->where('is_finish', true)
            ->whereDate('date', $this->selectedDate);

        if ($this->selectedWorker !== 'semua') {
            $query->whereHas('workers', function ($q) {
                $q->where('user_id', $this->selectedWorker);
            });
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Produk',
            'Jumlah Produksi',
            'Pekerja',
            'Status',
        ];
    }

    public function map($production): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            Carbon::parse($production->date)->format('d M Y'),
            $production->product->name ?? '-',
            $production->qty . ' ' . $production->product->unit->name ?? '-',
            $production->workers->pluck('name')->join(', '),
            $production->is_finish ? 'Selesai' : 'Dalam Proses',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
