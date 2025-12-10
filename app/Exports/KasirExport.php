<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KasirExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $selectedDate;

    protected $selectedWorker;

    protected $saleMethod;

    public function __construct($selectedDate, $selectedWorker = 'semua', $saleMethod = 'semua')
    {
        $this->selectedDate = $selectedDate;
        $this->selectedWorker = $selectedWorker;
        $this->saleMethod = $saleMethod;
    }

    public function collection()
    {
        $query = Transaction::with(['user', 'details.product'])
            ->where('status', 'Selesai')
            ->whereDate('start_date', $this->selectedDate);

        if ($this->selectedWorker !== 'semua') {
            $query->where('user_id', $this->selectedWorker);
        }

        if ($this->saleMethod !== 'semua') {
            $query->where('sale_method', $this->saleMethod);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kasir',
            'Metode Penjualan',
            'Total Transaksi',
            'Status',
        ];
    }

    public function map($transaction): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            Carbon::parse($transaction->start_date)->format('d M Y H:i'),
            $transaction->user->name ?? '-',
            ucfirst($transaction->sale_method ?? '-'),
            'Rp ' . number_format($transaction->total_price, 0, ',', '.'),
            $transaction->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
