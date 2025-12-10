<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoriExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $expenses = Expense::with(['expenseDetails.material.unit'])
            ->whereDate('expense_date', $this->selectedDate)
            ->get();

        $productionQuery = Production::with(['productionDetails.material.unit'])
            ->where('is_finish', true)
            ->whereDate('date', $this->selectedDate);

        if ($this->selectedWorker !== 'semua') {
            $productionQuery->whereHas('workers', function ($q) {
                $q->where('user_id', $this->selectedWorker);
            });
        }

        $productions = $productionQuery->get();

        $data = new Collection;

        foreach ($expenses as $expense) {
            foreach ($expense->expenseDetails as $detail) {
                $data->push([
                    'type' => 'Belanja',
                    'date' => $expense->expense_date,
                    'material' => $detail->material->name,
                    'qty' => $detail->qty,
                    'unit' => $detail->material->unit->name ?? '-',
                    'notes' => 'Pembelian bahan',
                ]);
            }
        }

        foreach ($productions as $production) {
            foreach ($production->productionDetails as $detail) {
                $data->push([
                    'type' => 'Produksi',
                    'date' => $production->date,
                    'material' => $detail->material->name,
                    'qty' => $detail->qty,
                    'unit' => $detail->material->unit->name ?? '-',
                    'notes' => 'Produksi ' . $production->product->name,
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tipe',
            'Tanggal',
            'Bahan',
            'Jumlah',
            'Satuan',
            'Keterangan',
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $item['type'],
            Carbon::parse($item['date'])->format('d M Y'),
            $item['material'],
            $item['qty'],
            $item['unit'],
            $item['notes'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
