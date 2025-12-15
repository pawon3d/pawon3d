<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Production;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Spatie\Activitylog\Models\Activity;

class KasirExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

    public function __construct($reportContent, $filterPeriod, $selectedDate, $customStartDate, $customEndDate, $selectedWorker = 'semua', $selectedMethod = 'semua')
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
            $this->startDate = Carbon::parse($this->customStartDate)->startOfDay();
            $this->endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : Carbon::parse($this->customStartDate)->endOfDay();
        } else {
            $selectedDateCarbon = Carbon::parse($this->selectedDate);

            switch ($this->filterPeriod) {
                case 'Hari':
                    $this->startDate = $selectedDateCarbon->copy()->startOfDay();
                    $this->endDate = $selectedDateCarbon->copy()->endOfDay();
                    break;
                case 'Minggu':
                    $this->startDate = $selectedDateCarbon->copy()->startOfWeek()->startOfDay();
                    $this->endDate = $selectedDateCarbon->copy()->endOfWeek()->endOfDay();
                    break;
                case 'Bulan':
                    $this->startDate = $selectedDateCarbon->copy()->startOfMonth()->startOfDay();
                    $this->endDate = $selectedDateCarbon->copy()->endOfMonth()->endOfDay();
                    break;
                case 'Tahun':
                    $this->startDate = $selectedDateCarbon->copy()->startOfYear()->startOfDay();
                    $this->endDate = $selectedDateCarbon->copy()->endOfYear()->endOfDay();
                    break;
                default:
                    $this->startDate = $selectedDateCarbon->copy()->startOfDay();
                    $this->endDate = $selectedDateCarbon->copy()->endOfDay();
            }
        }
    }

    public function collection()
    {
        switch ($this->reportContent) {
            case 'sesi':
                return $this->getSesiData();
            case 'customer':
                return $this->getCustomerData();
            case 'transaksi':
                return $this->getTransaksiData();
            case 'terjual':
                return $this->getTerjualData();
            case 'keuangan':
            default:
                return $this->getKeuanganData();
        }
    }

    private function getSesiData()
    {
        $shiftsQuery = \App\Models\Shift::with('openedBy')
            ->whereBetween('start_time', [$this->startDate, $this->endDate])
            ->where('status', 'closed');

        if ($this->selectedWorker !== 'semua') {
            $shiftsQuery->where('opened_by', $this->selectedWorker);
        }

        return $shiftsQuery->orderBy('start_time', 'desc')->get();
    }

    private function getCustomerData()
    {
        return Transaction::whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('status', 'Selesai')
            ->whereNotNull('phone')
            ->select('phone', 'name')
            ->selectRaw('MIN(start_date) as first_transaction')
            ->selectRaw('COUNT(*) as transaction_count')
            ->groupBy('phone', 'name')
            ->havingRaw('MIN(start_date) BETWEEN ? AND ?', [$this->startDate, $this->endDate])
            ->orderBy('first_transaction', 'desc')
            ->get();
    }

    private function getTransaksiData()
    {
        $query = Transaction::with(['details.product', 'user'])
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('status', 'Selesai');

        if ($this->selectedWorker !== 'semua') {
            $query->where('user_id', $this->selectedWorker);
        }

        if ($this->selectedMethod !== 'semua') {
            $query->where('method', $this->selectedMethod);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    private function getTerjualData()
    {
        $transactionsQuery = Transaction::whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('status', 'Selesai');

        if ($this->selectedWorker !== 'semua') {
            $transactionsQuery->where('user_id', $this->selectedWorker);
        }

        if ($this->selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $this->selectedMethod);
        }

        $transactions = $transactionsQuery->get();
        $transactionIds = $transactions->pluck('id');

        $details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();

        $products = Product::with('category')->get();
        $productSales = collect();

        foreach ($products as $product) {
            $sold = $details->where('product_id', $product->id)->sum(fn($d) => $d->quantity - $d->refund_quantity);
            if ($sold > 0) {
                $productionDetails = \App\Models\ProductionDetail::whereHas('production', function ($q) {
                    $q->whereBetween('start_date', [$this->startDate, $this->endDate]);
                })
                    ->where('product_id', $product->id)
                    ->sum('quantity_get');

                $productSales->push((object) [
                    'name' => $product->name,
                    'produksi' => $productionDetails,
                    'sold' => $sold,
                    'unsold' => max(0, $productionDetails - $sold),
                ]);
            }
        }

        return $productSales->sortByDesc('sold');
    }

    private function getKeuanganData()
    {
        $transactionsQuery = Transaction::whereBetween('start_date', [$this->startDate, $this->endDate])
            ->where('status', 'Selesai');

        if ($this->selectedWorker !== 'semua') {
            $transactionsQuery->where('user_id', $this->selectedWorker);
        }

        if ($this->selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $this->selectedMethod);
        }

        $transactions = $transactionsQuery->get();
        $transactionIds = $transactions->pluck('id');

        $details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();

        $keuanganData = collect();
        $year = Carbon::parse($this->startDate)->year;

        foreach (range(1, 12) as $month) {
            $monthTransactions = $transactions->filter(fn($trx) => Carbon::parse($trx->start_date)->month === $month);
            $monthDetails = $details->filter(function ($d) use ($month, $transactions) {
                $trx = $transactions->firstWhere('id', $d->transaction_id);

                return $trx && Carbon::parse($trx->start_date)->month === $month;
            });

            $pendapatanKotor = $monthTransactions->sum('total_amount');
            $refund = $monthTransactions->sum('total_refund');
            $potonganHarga = $monthTransactions->sum('points_discount');
            $pendapatanBersih = $pendapatanKotor - $refund - $potonganHarga;
            $modal = $monthDetails->sum(function ($d) {
                return ($d->pcs_capital_snapshot ?? 0) * ($d->quantity - $d->refund_quantity);
            });
            $keuntungan = $pendapatanBersih - $modal;

            if ($pendapatanKotor > 0 || $refund > 0) {
                $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');
                $keuanganData->push((object) [
                    'waktu' => $monthName,
                    'pendapatanKotor' => $pendapatanKotor,
                    'refund' => $refund,
                    'potonganHarga' => $potonganHarga,
                    'pendapatanBersih' => $pendapatanBersih,
                    'modal' => $modal,
                    'keuntungan' => $keuntungan,
                ]);
            }
        }

        return $keuanganData;
    }

    public function headings(): array
    {
        switch ($this->reportContent) {
            case 'sesi':
                return ['No', 'Tanggal', 'Kasir', 'Durasi', 'Total Transaksi', 'Total Pendapatan'];
            case 'customer':
                return ['No', 'Nomor HP', 'Nama', 'Jumlah Transaksi', 'Transaksi Pertama'];
            case 'transaksi':
                return ['No', 'Invoice', 'Tanggal', 'Kasir', 'Produk', 'Total', 'Metode'];
            case 'terjual':
                return ['No', 'Produk', 'Produksi', 'Terjual', 'Tidak Terjual'];
            case 'keuangan':
            default:
                return ['No', 'Waktu', 'Pendapatan Kotor', 'Refund', 'Potongan Harga', 'Pendapatan Bersih', 'Modal', 'Keuntungan'];
        }
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        switch ($this->reportContent) {
            case 'sesi':
                $duration = '-';
                if ($item->start_time && $item->end_time) {
                    $start = Carbon::parse($item->start_time);
                    $end = Carbon::parse($item->end_time);
                    $diff = $start->diff($end);
                    $duration = $diff->format('%H jam %I menit');
                }

                $totalTransaksi = Transaction::where('created_by_shift', $item->id)->count();
                $totalPendapatan = Transaction::where('created_by_shift', $item->id)->sum('total_amount') ?? 0;

                return [
                    $index,
                    Carbon::parse($item->start_time)->translatedFormat('d M Y H:i'),
                    $item->openedBy->name ?? '-',
                    $duration,
                    $totalTransaksi,
                    'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
                ];
            case 'customer':
                return [
                    $index,
                    $item->phone,
                    $item->name ?? '-',
                    $item->transaction_count,
                    Carbon::parse($item->first_transaction)->translatedFormat('d M Y H:i'),
                ];
            case 'transaksi':
                $products = $item->details->map(fn($d) => $d->product->name . ' (' . $d->quantity . ')')->implode(', ');

                return [
                    $index,
                    $item->invoice_number,
                    Carbon::parse($item->start_date)->translatedFormat('d M Y H:i'),
                    $item->user->name ?? '-',
                    $products,
                    'Rp ' . number_format($item->total_amount, 0, ',', '.'),
                    $item->method ? ucfirst(str_replace('-', ' ', $item->method)) : 'Tidak Diketahui',
                ];
            case 'terjual':
                return [
                    $index,
                    $item->name,
                    $item->produksi,
                    $item->sold,
                    $item->unsold,
                ];
            case 'keuangan':
            default:
                return [
                    $index,
                    $item->waktu,
                    'Rp ' . number_format($item->pendapatanKotor, 0, ',', '.'),
                    'Rp ' . number_format($item->refund, 0, ',', '.'),
                    'Rp ' . number_format($item->potonganHarga, 0, ',', '.'),
                    'Rp ' . number_format($item->pendapatanBersih, 0, ',', '.'),
                    'Rp ' . number_format($item->modal, 0, ',', '.'),
                    'Rp ' . number_format($item->keuntungan, 0, ',', '.'),
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
