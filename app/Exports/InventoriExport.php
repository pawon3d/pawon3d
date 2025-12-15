<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\InventoryLog;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductionDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoriExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $reportContent;

    protected $filterPeriod;

    protected $selectedDate;

    protected $customStartDate;

    protected $customEndDate;

    protected $selectedWorker;

    public function __construct($reportContent, $filterPeriod, $selectedDate, $customStartDate, $customEndDate, $selectedWorker)
    {
        $this->reportContent = $reportContent;
        $this->filterPeriod = $filterPeriod;
        $this->selectedDate = $selectedDate;
        $this->customStartDate = $customStartDate;
        $this->customEndDate = $customEndDate;
        $this->selectedWorker = $selectedWorker;
    }

    public function collection()
    {
        // Determine date range
        if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
            $startDate = Carbon::parse($this->customStartDate)->startOfDay();
            $endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : Carbon::parse($this->customStartDate)->endOfDay();
        } else {
            $selectedDateCarbon = Carbon::parse($this->selectedDate);

            switch ($this->filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek();
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth();
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear();
                    $endDate = $selectedDateCarbon->copy()->endOfYear();
                    break;
                default:
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
            }
        }

        // Load data based on reportContent
        if ($this->reportContent === 'belanja') {
            return $this->getBelanjaData($startDate, $endDate);
        } elseif ($this->reportContent === 'persediaan') {
            return $this->getPersediaanData($startDate, $endDate);
        } elseif ($this->reportContent === 'alur') {
            return $this->getAlurData($startDate, $endDate);
        }

        return new Collection;
    }

    protected function getBelanjaData($startDate, $endDate)
    {
        $expensesQuery = Expense::with(['supplier', 'expenseDetails.material', 'expenseDetails.unit'])
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($this->selectedWorker !== 'semua') {
            $expenseIds = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                ->where('causer_id', $this->selectedWorker)
                ->pluck('subject_id')
                ->unique();

            $expensesQuery->whereIn('id', $expenseIds);
        }

        $expenses = $expensesQuery->get();

        $data = new Collection;

        foreach ($expenses as $expense) {
            $workerName = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                ->where('subject_id', $expense->id)
                ->latest()
                ->first()?->causer->name ?? '-';

            foreach ($expense->expenseDetails as $detail) {
                $data->push([
                    'type' => 'belanja',
                    'expense_number' => $expense->expense_number,
                    'expense_date' => Carbon::parse($expense->expense_date)->translatedFormat('d F Y'),
                    'supplier_name' => $expense->supplier->name ?? '-',
                    'worker_name' => $workerName,
                    'status' => $expense->status,
                    'material_name' => $detail->material->name ?? '-',
                    'unit_name' => $detail->unit->name ?? '-',
                    'quantity_expect' => $detail->quantity_expect,
                    'quantity_get' => $detail->quantity_get,
                    'price_expect' => $detail->price_expect,
                    'price_get' => $detail->price_get,
                    'total_expect' => $detail->total_expect,
                    'total_actual' => $detail->total_actual,
                    'grand_total' => '', // Only on first detail
                ]);
            }

            // Update first row with grand total
            if ($data->count() > 0 && $data->last()['expense_number'] === $expense->expense_number) {
                $firstIndex = $data->search(fn($item) => $item['expense_number'] === $expense->expense_number);
                if ($firstIndex !== false) {
                    $firstItem = $data->get($firstIndex);
                    $firstItem['grand_total'] = $expense->grand_total_actual;
                    $data->put($firstIndex, $firstItem);
                }
            }
        }

        return $data;
    }

    protected function getPersediaanData($startDate, $endDate)
    {
        // Calculate cumulative values
        $cumulativeExpenseDetails = ExpenseDetail::with('material')
            ->whereHas('expense', function ($query) use ($endDate) {
                $query->where('expense_date', '<=', $endDate->toDateString());
            })
            ->get();

        $grandTotal = 0;
        foreach ($cumulativeExpenseDetails as $detail) {
            $grandTotal += $detail->total_actual;
        }

        // Calculate production usage
        $usedGrandTotal = 0;
        $materialUsage = [];

        $products = Product::with(['product_compositions.material'])->get();
        $groupProductByMaterial = $products->flatMap(function ($product) {
            return $product->product_compositions->map(function ($composition) use ($product) {
                return [
                    'material_id' => $composition->material_id,
                    'material_quantity' => $composition->material_quantity,
                    'unit_id' => $composition->unit_id,
                    'pcs' => $product->pcs,
                    'product_id' => $product->id,
                    'material_name' => $composition->material->name ?? 'Unknown',
                ];
            });
        })->groupBy('material_id');

        foreach ($groupProductByMaterial as $materialId => $compositions) {
            foreach ($compositions as $composition) {
                $productId = $composition['product_id'];

                $productionDetailsQuery = ProductionDetail::where('product_id', $productId)
                    ->whereHas('production', function ($query) use ($endDate) {
                        $query->where('start_date', '<=', $endDate->toDateString());

                        if ($this->selectedWorker !== 'semua') {
                            $query->whereHas('workers', function ($workerQuery) {
                                $workerQuery->where('user_id', $this->selectedWorker);
                            });
                        }
                    });

                $productionDetails = $productionDetailsQuery->get();
                $totalProduction = $productionDetails->sum('quantity_get') + $productionDetails->sum('quantity_fail');
                $dividedQuantity = $composition['pcs'] > 0 ? $totalProduction / $composition['pcs'] : 0;
                $totalMaterialQuantity = $dividedQuantity * $composition['material_quantity'];
                $material = Material::find($materialId);
                // Use supply_price instead of price_get
                $materialPrice = $material->material_details->where('unit_id', $composition['unit_id'])->first()->supply_price ?? 0;
                $priceValue = $totalMaterialQuantity * $materialPrice;

                if (! isset($materialUsage[$materialId])) {
                    $materialUsage[$materialId] = [
                        'material_name' => $composition['material_name'],
                        'quantity_used' => 0,
                        'value_used' => 0,
                    ];
                }

                $materialUsage[$materialId]['quantity_used'] += $totalMaterialQuantity;
                $materialUsage[$materialId]['value_used'] += $priceValue;
                $usedGrandTotal += $priceValue;
            }
        }

        $remainGrandTotal = $grandTotal - $usedGrandTotal;

        // Build collection
        $data = new Collection;

        // Summary section header
        $data->push([
            'type' => 'persediaan_header',
            'col1' => 'RINGKASAN PERSEDIAAN',
            'col2' => '',
            'col3' => '',
            'col4' => '',
        ]);

        // Summary rows
        $data->push([
            'type' => 'persediaan',
            'col1' => 'Nilai Persediaan',
            'col2' => 'Rp ' . number_format($grandTotal, 0, ',', '.'),
            'col3' => '',
            'col4' => '',
        ]);

        $data->push([
            'type' => 'persediaan',
            'col1' => 'Nilai Persediaan Terpakai',
            'col2' => 'Rp ' . number_format($usedGrandTotal, 0, ',', '.'),
            'col3' => '',
            'col4' => '',
        ]);

        $data->push([
            'type' => 'persediaan',
            'col1' => 'Nilai Persediaan Saat Ini',
            'col2' => 'Rp ' . number_format($remainGrandTotal, 0, ',', '.'),
            'col3' => '',
            'col4' => '',
        ]);

        // Empty row separator
        $data->push([
            'type' => 'persediaan_separator',
            'col1' => '',
            'col2' => '',
            'col3' => '',
            'col4' => '',
        ]);

        // Material usage section header
        $data->push([
            'type' => 'persediaan_header',
            'col1' => 'DETAIL PENGGUNAAN MATERIAL',
            'col2' => '',
            'col3' => '',
            'col4' => '',
        ]);

        // Material usage header row
        $data->push([
            'type' => 'persediaan_detail_header',
            'col1' => 'Material',
            'col2' => 'Jumlah Digunakan',
            'col3' => 'Nilai (Rp)',
            'col4' => '',
        ]);

        // Material usage details
        foreach ($materialUsage as $material) {
            $data->push([
                'type' => 'persediaan_detail',
                'col1' => $material['material_name'],
                'col2' => number_format($material['quantity_used'], 2, ',', '.'),
                'col3' => 'Rp ' . number_format($material['value_used'], 0, ',', '.'),
                'col4' => '',
            ]);
        }

        return $data;
    }

    protected function getAlurData($startDate, $endDate)
    {
        $flowQuery = InventoryLog::with(['material', 'materialBatch.unit', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($this->selectedWorker !== 'semua') {
            $flowQuery->where('user_id', $this->selectedWorker);
        }

        $flows = $flowQuery->orderBy('created_at', 'desc')->get();

        $data = new Collection;

        foreach ($flows as $log) {
            $data->push([
                'type' => 'alur',
                'created_at' => Carbon::parse($log->created_at)->translatedFormat('d F Y H:i'),
                'material_name' => $log->material->name ?? '-',
                'batch_number' => $log->materialBatch->batch_number ?? '-',
                'unit_name' => $log->materialBatch->unit->name ?? '-',
                'action' => $log->action,
                'quantity_change' => $log->quantity_change,
                'quantity_after' => $log->quantity_after,
                'user_name' => $log->user->name ?? '-',
                'note' => $log->note ?? '-',
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        if ($this->reportContent === 'belanja') {
            return [
                'No',
                'No. Belanja',
                'Tanggal',
                'Supplier',
                'Pekerja',
                'Status',
                'Material',
                'Satuan',
                'Qty Harap',
                'Qty Dapat',
                'Harga Harap',
                'Harga Dapat',
                'Total Harap',
                'Total Dapat',
                'Grand Total',
            ];
        } elseif ($this->reportContent === 'persediaan') {
            return [
                'No',
                'Keterangan',
                'Jumlah/Nilai',
                'Nilai (Rp)',
            ];
        } elseif ($this->reportContent === 'alur') {
            return [
                'No',
                'Tanggal',
                'Material',
                'Batch',
                'Satuan',
                'Aksi',
                'Perubahan',
                'Stok Setelah',
                'Pekerja',
                'Catatan',
            ];
        }

        return ['No'];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        if ($item['type'] === 'belanja') {
            return [
                $index,
                $item['expense_number'],
                $item['expense_date'],
                $item['supplier_name'],
                $item['worker_name'],
                $item['status'],
                $item['material_name'],
                $item['unit_name'],
                $item['quantity_expect'],
                $item['quantity_get'],
                $item['price_expect'],
                $item['price_get'],
                $item['total_expect'],
                $item['total_actual'],
                $item['grand_total'],
            ];
        } elseif (in_array($item['type'], ['persediaan', 'persediaan_header', 'persediaan_separator', 'persediaan_detail_header', 'persediaan_detail'])) {
            // Skip index increment for header/separator rows
            if (in_array($item['type'], ['persediaan_header', 'persediaan_separator', 'persediaan_detail_header'])) {
                $index--;
            }
            return [
                $item['type'] === 'persediaan_header' || $item['type'] === 'persediaan_detail_header' ? '' : ($item['type'] === 'persediaan_separator' ? '' : $index),
                $item['col1'],
                $item['col2'],
                $item['col3'],
            ];
        } elseif ($item['type'] === 'alur') {
            return [
                $index,
                $item['created_at'],
                $item['material_name'],
                $item['batch_number'],
                $item['unit_name'],
                $item['action'],
                $item['quantity_change'],
                $item['quantity_after'],
                $item['user_name'],
                $item['note'],
            ];
        }

        return [$index];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return ucfirst($this->reportContent);
    }
}