<?php

namespace App\Livewire\Hitung;

use App\Models\Hitung;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Form extends Component
{
    public ?string $hitung_id = null;

    public string $action = '';

    public string $hitung_date = '';

    public ?string $note = '';

    public float $grand_total = 0;

    public array $hitung_details = [];

    protected $messages = [
        'action.required' => 'Aksi harus dipilih.',
        'hitung_details.*.material_id.required' => 'Barang persediaan harus dipilih.',
        'hitung_details.*.material_batch_id.required' => 'Batch harus dipilih.',
    ];

    public function mount(?string $id = null)
    {
        $this->hitung_id = $id;

        if ($this->isEditMode()) {
            View::share('title', 'Ubah Aksi');
            $this->loadHitung();
        } else {
            View::share('title', 'Tambah Aksi');
            $this->initializeEmptyDetail();
        }

        View::share('mainTitle', 'Inventori');
    }

    public function isEditMode(): bool
    {
        return ! empty($this->hitung_id);
    }

    protected function loadHitung(): void
    {
        $hitung = Hitung::with(['details.material.batches.unit', 'details.material.material_details'])
            ->findOrFail($this->hitung_id);

        $this->action = $hitung->action;
        $this->hitung_date = $hitung->hitung_date ? \Carbon\Carbon::parse($hitung->hitung_date)->format('d M Y') : '';
        $this->note = $hitung->note;
        $this->grand_total = $hitung->grand_total ?? 0;

        $this->hitung_details = $hitung->details->map(function ($detail) {
            $batch = $detail->material?->batches->firstWhere('id', $detail->material_batch_id);

            return [
                'material_id' => $detail->material_id,
                'material_batch_id' => $detail->material_batch_id,
                'material_quantity' => $batch?->batch_quantity ?? 0,
                'quantity_actual' => $detail->quantity_actual ?? 0,
                'unit_name' => ' ('.($batch?->unit?->alias ?? '-').')',
                'total' => $detail->total ?? 0,
            ];
        })->toArray();

        if (empty($this->hitung_details)) {
            $this->initializeEmptyDetail();
        }
    }

    protected function initializeEmptyDetail(): void
    {
        $this->hitung_details = [[
            'material_id' => '',
            'material_batch_id' => '',
            'material_quantity' => 0,
            'quantity_actual' => 0,
            'unit_name' => ' (satuan)',
            'total' => 0,
        ]];
    }

    public function addDetail(): void
    {
        $this->hitung_details[] = [
            'material_id' => '',
            'material_batch_id' => '',
            'material_quantity' => 0,
            'quantity_actual' => 0,
            'unit_name' => ' (satuan)',
            'total' => 0,
        ];
    }

    public function removeDetail(int $index): void
    {
        if (count($this->hitung_details) > 1) {
            unset($this->hitung_details[$index]);
            $this->hitung_details = array_values($this->hitung_details);
            $this->calculateGrandTotal();
        }
    }

    public function setMaterial(int $index, $materialId): void
    {
        if ($materialId) {
            $this->hitung_details[$index]['material_id'] = $materialId;
            // Reset batch ketika material berubah
            $this->hitung_details[$index]['material_batch_id'] = '';
            $this->hitung_details[$index]['material_quantity'] = 0;
            $this->hitung_details[$index]['unit_name'] = ' (satuan)';
            $this->hitung_details[$index]['total'] = 0;
        } else {
            $this->hitung_details[$index]['material_id'] = '';
            $this->hitung_details[$index]['material_batch_id'] = '';
            $this->hitung_details[$index]['material_quantity'] = 0;
            $this->hitung_details[$index]['unit_name'] = ' (satuan)';
            $this->hitung_details[$index]['total'] = 0;
        }
        $this->calculateGrandTotal();
    }

    public function setBatch(int $index, $batchId): void
    {
        if ($batchId && $this->hitung_details[$index]['material_id']) {
            $material = Material::with(['batches.unit', 'material_details'])
                ->find($this->hitung_details[$index]['material_id']);
            $batch = $material?->batches->firstWhere('id', $batchId);

            if ($batch) {
                $this->hitung_details[$index]['material_batch_id'] = $batchId;
                $this->hitung_details[$index]['material_quantity'] = $batch->batch_quantity ?? 0;
                $this->hitung_details[$index]['unit_name'] = ' ('.($batch->unit?->alias ?? '-').')';

                $price = $material->material_details->firstWhere('unit_id', $batch->unit_id)?->supply_price ?? 0;
                $this->hitung_details[$index]['total'] = $this->hitung_details[$index]['material_quantity'] * $price;
            }
        } else {
            $this->hitung_details[$index]['material_batch_id'] = '';
            $this->hitung_details[$index]['material_quantity'] = 0;
            $this->hitung_details[$index]['unit_name'] = ' (satuan)';
            $this->hitung_details[$index]['total'] = 0;
        }
        $this->calculateGrandTotal();
    }

    public function calculateGrandTotal(): void
    {
        $this->grand_total = array_sum(array_column($this->hitung_details, 'total'));
    }

    public function updatedAction($value): void
    {
        $this->action = $value;
    }

    protected function getValidationRules(): array
    {
        return [
            'action' => 'required|string|max:255',
            'hitung_date' => $this->hitung_date ? 'date_format:d M Y' : 'nullable',
            'note' => 'nullable|string|max:255',
            'grand_total' => 'nullable|numeric|min:0',
            'hitung_details.*.material_id' => 'required|exists:materials,id',
            'hitung_details.*.material_quantity' => 'nullable|numeric|min:0',
            'hitung_details.*.material_batch_id' => 'required|exists:material_batches,id',
            'hitung_details.*.total' => 'nullable|numeric|min:0',
        ];
    }

    protected function getValidationMessages(): array
    {
        return [
            'action.required' => 'Pilih jenis aksi terlebih dahulu.',
            'hitung_details.*.material_id.required' => 'Pilih barang persediaan terlebih dahulu.',
            'hitung_details.*.material_batch_id.required' => 'Pilih batch persediaan terlebih dahulu.',
        ];
    }

    protected function calculateLossTotal(array $detail, string $action): float
    {
        $materialQuantity = $detail['material_quantity'] ?? 0;
        $quantityActual = $detail['quantity_actual'] ?? 0;
        $total = $detail['total'] ?? 0;

        if ($materialQuantity <= 0) {
            return 0;
        }

        $pricePerUnit = $total / $materialQuantity;

        if ($action === 'Hitung Persediaan') {
            return $pricePerUnit * ($quantityActual - $materialQuantity);
        }

        return $pricePerUnit * $quantityActual;
    }

    protected function saveDetails(Hitung $hitung): void
    {
        foreach ($this->hitung_details as $detail) {
            if (empty($detail['material_id'])) {
                continue;
            }

            $hitung->details()->create([
                'material_id' => $detail['material_id'],
                'material_batch_id' => $detail['material_batch_id'],
                'quantity_expect' => $detail['material_quantity'] ?? 0,
                'quantity_actual' => $detail['quantity_actual'] ?? 0,
                'total' => $detail['total'] ?? 0,
                'loss_total' => $this->calculateLossTotal($detail, $hitung->action),
            ]);
        }

        $hitung->update(['loss_grand_total' => $hitung->details->sum('loss_total')]);
    }

    /**
     * Simpan rencana aksi (buat baru atau update)
     */
    public function save()
    {
        $this->validate($this->getValidationRules(), $this->getValidationMessages());

        if ($this->isEditMode()) {
            return $this->update();
        }

        return $this->store();
    }

    /**
     * Simpan rencana aksi baru
     */
    protected function store()
    {
        $hitung = Hitung::create([
            'user_id' => Auth::id(),
            'action' => $this->action ?? 'Hitung Persediaan',
            'hitung_date' => $this->hitung_date ? \Carbon\Carbon::createFromFormat('d M Y', $this->hitung_date)->format('Y-m-d') : null,
            'note' => $this->note,
            'grand_total' => $this->grand_total,
            'status' => 'Belum Diproses',
            'is_start' => false,
            'is_finish' => false,
        ]);

        $this->saveDetails($hitung);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($hitung)
            ->event('created')
            ->withProperties(['action' => $hitung->action, 'status' => 'Belum Diproses'])
            ->log('Membuat rencana aksi '.$hitung->action);

        return redirect()->route('hitung.rencana')->with('success', 'Rencana aksi berhasil dibuat.');
    }

    /**
     * Update rencana aksi yang sudah ada
     */
    protected function update()
    {
        $hitung = Hitung::findOrFail($this->hitung_id);

        $hitung->update([
            'action' => $this->action ?? 'Hitung Persediaan',
            'hitung_date' => $this->hitung_date ? \Carbon\Carbon::createFromFormat('d M Y', $this->hitung_date)->format('Y-m-d') : null,
            'note' => $this->note,
            'grand_total' => $this->grand_total,
        ]);

        // Hapus detail lama sebelum menambahkan yang baru
        $hitung->details()->delete();
        $this->saveDetails($hitung);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($hitung)
            ->event('updated')
            ->withProperties(['action' => $hitung->action])
            ->log('Memperbarui rencana aksi '.$hitung->action);

        return redirect()->route('hitung.rincian', ['id' => $hitung->id])->with('success', 'Aksi berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.hitung.form', [
            'materials' => Material::with(['batches.unit', 'material_details'])->orderBy('name')->get(),
        ]);
    }
}
