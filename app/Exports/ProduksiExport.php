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
    protected $reportContent;

    protected $filterPeriod;

    protected $selectedDate;

    protected $customStartDate;

    protected $customEndDate;

    protected $selectedWorker;

    protected $selectedMethod;

    protected $startDate;

    protected $endDate;

    public function __construct($reportContent, $filterPeriod, $selectedDate, $customStartDate, $customEndDate, $selectedWorker, $selectedMethod)
    {
        $this->reportContent = $reportContent;
        $this->filterPeriod = $filterPeriod;
        $this->selectedDate = $selectedDate;
        $this->customStartDate = $customStartDate;
        $this->customEndDate = $customEndDate;
        $this->selectedWorker = $selectedWorker;
        $this->selectedMethod = $selectedMethod;

        $this->determineDateRange();
    }

    private function determineDateRange()
    {
        if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
            $this->startDate = Carbon::parse($this->customStartDate)->toDateString();
            $this->endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->toDateString() : $this->startDate;
        } else {
            $selectedDateCarbon = Carbon::parse($this->selectedDate);

            switch ($this->filterPeriod) {
                case 'Hari':
                    $this->startDate = $selectedDateCarbon->toDateString();
                    $this->endDate = $selectedDateCarbon->toDateString();
                    break;
                case 'Minggu':
                    $this->startDate = $selectedDateCarbon->copy()->startOfWeek()->toDateString();
                    $this->endDate = $selectedDateCarbon->copy()->endOfWeek()->toDateString();
                    break;
                case 'Bulan':
                    $this->startDate = $selectedDateCarbon->copy()->startOfMonth()->toDateString();
                    $this->endDate = $selectedDateCarbon->copy()->endOfMonth()->toDateString();
                    break;
                case 'Tahun':
                    $this->startDate = $selectedDateCarbon->copy()->startOfYear()->toDateString();
                    $this->endDate = $selectedDateCarbon->copy()->endOfYear()->toDateString();
                    break;
                default:
                    $this->startDate = $selectedDateCarbon->toDateString();
                    $this->endDate = $selectedDateCarbon->toDateString();
            }
        }
    }

    public function collection()
    {
        switch ($this->reportContent) {
            case 'produksi':
                return $this->getProduksiData();
            case 'berhasil':
                return $this->getBerhasilData();
            case 'gagal':
                return $this->getGagalData();
            default:
                return $this->getProduksiData();
        }
    }

    private function getProduksiData()
    {
        $query = Production::with(['details.product', 'workers.worker'])
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('is_finish', true);

        if ($this->selectedWorker !== 'semua') {
            $query->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
        }

        if ($this->selectedMethod !== 'semua') {
            $query->where('method', $this->selectedMethod);
        }

        $productions = $query->orderBy('start_date', 'desc')->get();

        $result = collect();
        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                $result->push((object) [
                    'date' => $production->start_date,
                    'product' => $detail->product->name ?? '-',
                    'total' => $detail->quantity_get + $detail->quantity_fail,
                    'success' => $detail->quantity_get,
                    'fail' => $detail->quantity_fail,
                    'workers' => $production->workers->map(fn($w) => $w->worker->name ?? '-')->join(', '),
                    'method' => $production->method,
                ]);
            }
        }

        return $result;
    }

    private function getBerhasilData()
    {
        $query = Production::with(['details.product', 'workers.worker'])
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('is_finish', true);

        if ($this->selectedWorker !== 'semua') {
            $query->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
        }

        if ($this->selectedMethod !== 'semua') {
            $query->where('method', $this->selectedMethod);
        }

        $productions = $query->orderBy('start_date', 'desc')->get();

        $result = collect();
        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                if ($detail->quantity_get > 0) {
                    $result->push((object) [
                        'date' => $production->start_date,
                        'product' => $detail->product->name ?? '-',
                        'success' => $detail->quantity_get,
                        'workers' => $production->workers->map(fn($w) => $w->worker->name ?? '-')->join(', '),
                        'method' => $production->method,
                    ]);
                }
            }
        }

        return $result;
    }

    private function getGagalData()
    {
        $query = Production::with(['details.product', 'workers.worker'])
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('is_finish', true);

        if ($this->selectedWorker !== 'semua') {
            $query->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
        }

        if ($this->selectedMethod !== 'semua') {
            $query->where('method', $this->selectedMethod);
        }

        $productions = $query->orderBy('start_date', 'desc')->get();

        $result = collect();
        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                if ($detail->quantity_fail > 0) {
                    $result->push((object) [
                        'date' => $production->start_date,
                        'product' => $detail->product->name ?? '-',
                        'fail' => $detail->quantity_fail,
                        'workers' => $production->workers->map(fn($w) => $w->worker->name ?? '-')->join(', '),
                        'method' => $production->method,
                    ]);
                }
            }
        }

        return $result;
    }

    public function headings(): array
    {
        switch ($this->reportContent) {
            case 'produksi':
                return ['No', 'Tanggal', 'Produk', 'Total', 'Berhasil', 'Gagal', 'Pekerja', 'Metode'];
            case 'berhasil':
                return ['No', 'Tanggal', 'Produk', 'Berhasil', 'Pekerja', 'Metode'];
            case 'gagal':
                return ['No', 'Tanggal', 'Produk', 'Gagal', 'Pekerja', 'Metode'];
            default:
                return ['No', 'Tanggal', 'Produk', 'Total', 'Berhasil', 'Gagal', 'Pekerja', 'Metode'];
        }
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        switch ($this->reportContent) {
            case 'produksi':
                return [
                    $index,
                    Carbon::parse($item->date)->translatedFormat('d M Y'),
                    $item->product,
                    $item->total,
                    $item->success,
                    $item->fail,
                    $item->workers,
                    $item->method ? ucfirst(str_replace('-', ' ', $item->method)) : 'Tidak Diketahui',
                ];
            case 'berhasil':
                return [
                    $index,
                    Carbon::parse($item->date)->translatedFormat('d M Y'),
                    $item->product,
                    $item->success,
                    $item->workers,
                    $item->method ? ucfirst(str_replace('-', ' ', $item->method)) : 'Tidak Diketahui',
                ];
            case 'gagal':
                return [
                    $index,
                    Carbon::parse($item->date)->translatedFormat('d M Y'),
                    $item->product,
                    $item->fail,
                    $item->workers,
                    $item->method ? ucfirst(str_replace('-', ' ', $item->method)) : 'Tidak Diketahui',
                ];
            default:
                return [
                    $index,
                    Carbon::parse($item->date)->translatedFormat('d M Y'),
                    $item->product,
                    $item->total,
                    $item->success,
                    $item->fail,
                    $item->workers,
                    $item->method ? ucfirst(str_replace('-', ' ', $item->method)) : 'Tidak Diketahui',
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
