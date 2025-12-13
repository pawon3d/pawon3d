<?php

namespace App\Livewire\Hitung;

use App\Models\Hitung;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public string $hitung_id = '';

    public bool $showHistoryModal = false;

    public bool $showNoteModal = false;

    public string $editNote = '';

    public array $activityLogs = [];

    public bool $is_start = false;

    public bool $is_finish = false;

    public string $status = '';

    public ?string $finish_date = null;

    protected $listeners = [
        'delete',
        'cancel',
    ];

    #[Computed]
    public function hitung(): Hitung
    {
        return Hitung::with(['details', 'details.material', 'details.materialBatch.unit'])
            ->findOrFail($this->hitung_id);
    }

    #[Computed]
    public function hitungDetails()
    {
        return $this->hitung->details;
    }

    public function mount($id)
    {
        $this->hitung_id = $id;
        $hitung = $this->hitung;
        $this->is_start = (bool) $hitung->is_start;
        $this->is_finish = (bool) $hitung->is_finish;
        $this->status = $hitung->status ?? 'Belum Diproses';
        $this->finish_date = $hitung->hitung_date_finish;
        View::share('title', 'Rincian ' . $hitung->action);
        View::share('mainTitle', 'Inventori');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function riwayatPembaruan()
    {
        $logs = Activity::inLog('hitungs')
            ->where('subject_id', $this->hitung_id)
            ->with('causer')
            ->latest()
            ->limit(50)
            ->get();

        $this->activityLogs = $logs->map(fn($log) => [
            'description' => $log->description,
            'causer_name' => $log->causer->name ?? 'System',
            'created_at' => $log->created_at->format('d M Y H:i'),
        ])->toArray();

        $this->showHistoryModal = true;
    }

    public function openNoteModal()
    {
        $this->editNote = $this->hitung->note ?? '';
        $this->showNoteModal = true;
    }

    public function saveNote()
    {
        $hitung = Hitung::findOrFail($this->hitung_id);
        $hitung->update(['note' => $this->editNote]);
        unset($this->hitung); // Clear computed property cache
        $this->showNoteModal = false;
        $this->alert('success', 'Catatan berhasil disimpan.');
    }

    public function cetakInformasi()
    {
        return redirect()->route('rincian-hitung.pdf', [
            'id' => $this->hitung_id,
        ]);
    }

    public function confirmDelete()
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus aksi ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $hitung = Hitung::findOrFail($this->hitung_id);
        if ($hitung) {
            $hitungNumber = $hitung->hitung_number;
            $hitung->delete();

            // Kirim notifikasi penghitungan dibatalkan
            NotificationService::stockCountCancelled($hitungNumber);

            return redirect()->intended(route('hitung'))->with('success', 'Aksi berhasil dihapus!');
        } else {
            $this->alert('error', 'Aksi tidak ditemukan!');
        }
    }

    public function cancelAction()
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin membatalkan aksi ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, batalkan',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'cancel',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function cancel()
    {
        $hitung = Hitung::findOrFail($this->hitung_id);
        if ($hitung) {
            $hitung->update(['is_start' => true, 'is_finish' => true, 'status' => 'Dibatalkan', 'hitung_date_finish' => null]);

            // Kirim notifikasi penghitungan dibatalkan
            NotificationService::stockCountCancelled($hitung->hitung_number);

            return redirect()->intended(route('hitung'))->with('success', 'Aksi berhasil dibatalkan!');
        } else {
            $this->alert('error', 'Aksi tidak ditemukan!');
        }
    }

    public function start()
    {
        $this->is_start = true;
        $this->status = 'Sedang Diproses';
        $hitung = Hitung::findOrFail($this->hitung_id);
        $hitung->update(['is_start' => $this->is_start, 'status' => $this->status]);

        // Kirim notifikasi penghitungan dimulai
        NotificationService::stockCountStarted($hitung->hitung_number);

        unset($this->hitung); // Clear computed property cache
        $this->alert('success', $hitung->action . ' berhasil dimulai.');
    }

    public function finish()
    {
        $this->is_finish = true;
        $this->status = 'Selesai';
        $this->finish_date = now()->format('Y-m-d');
        $hitung = Hitung::findOrFail($this->hitung_id);

        // Update status hitung
        $hitung->update([
            'is_finish' => $this->is_finish,
            'status' => 'Selesai',
            'hitung_date_finish' => $this->finish_date,
        ]);

        // Update MaterialBatch berdasarkan action type
        $affectedMaterialIds = collect();

        $hitung->details->each(function ($detail) use ($hitung, &$affectedMaterialIds) {
            $batch = $detail->materialBatch;
            if (! $batch) {
                return;
            }

            // Track material yang terpengaruh
            $affectedMaterialIds->push($batch->material_id);

            if ($hitung->action === 'Hitung Persediaan') {
                // Untuk Hitung Persediaan:
                // quantity_actual = jumlah yang dihitung secara fisik
                // quantity_expect = jumlah yang diharapkan (dari batch_quantity sebelumnya)
                // Selisih = quantity_actual - quantity_expect
                // Jika selisih negatif = kekurangan (batch berkurang)
                // Jika selisih positif = kelebihan (batch bertambah)
                // Update batch_quantity menjadi quantity_actual (hasil hitung fisik)
                $quantityBefore = $batch->batch_quantity;
                $quantityChange = $detail->quantity_actual - $quantityBefore;

                // Create inventory log untuk penyesuaian stok
                \App\Models\InventoryLog::create([
                    'material_id' => $batch->material_id,
                    'material_batch_id' => $batch->id,
                    'user_id' => Auth::user()->id,
                    'action' => 'hitung',
                    'quantity_change' => $quantityChange,
                    'quantity_after' => $detail->quantity_actual,
                    'reference_type' => 'hitung',
                    'reference_id' => $hitung->id,
                    'note' => "Hitung Persediaan: {$hitung->hitung_number} - {$detail->material->name}",
                ]);

                // Jika sebelumnya 0 dan hasil hitung juga 0, hapus batch
                if ($quantityBefore == 0 && $detail->quantity_actual == 0) {
                    $batch->delete();
                } else {
                    $batch->update([
                        'batch_quantity' => $detail->quantity_actual,
                    ]);
                }
            } else {
                // Untuk Catat Persediaan Rusak / Hilang:
                // quantity_actual = jumlah yang rusak/hilang
                // Kurangi batch_quantity sebesar quantity_actual
                $quantityBefore = $batch->batch_quantity;
                $newBatchQuantity = max(0, $batch->batch_quantity - $detail->quantity_actual);

                // Determine action type based on hitung action
                $actionType = $hitung->action === 'Catat Persediaan Rusak' ? 'rusak' : 'hilang';

                // Jika batch quantity menjadi 0 dan batch sudah expired, hapus batch
                $isExpired = $batch->date && now()->greaterThan($batch->date);

                // Create inventory log sebelum delete/update
                \App\Models\InventoryLog::create([
                    'material_id' => $batch->material_id,
                    'material_batch_id' => $batch->id,
                    'user_id' => Auth::user()->id,
                    'action' => $actionType,
                    'quantity_change' => -$detail->quantity_actual,
                    'quantity_after' => $newBatchQuantity,
                    'reference_type' => 'hitung',
                    'reference_id' => $hitung->id,
                    'note' => "{$hitung->action}: {$hitung->hitung_number} - {$detail->material->name}",
                ]);

                if ($newBatchQuantity <= 0 && $isExpired) {
                    $batch->delete();
                } else {
                    $batch->update([
                        'batch_quantity' => $newBatchQuantity,
                    ]);
                }
            }
        });

        // Recalculate status for all affected materials
        $affectedMaterialIds->unique()->each(function ($materialId) {
            $material = \App\Models\Material::find($materialId);
            if ($material) {
                $material->recalculateStatus();
            }
        });

        unset($this->hitung); // Clear computed property cache

        // Kirim notifikasi penghitungan selesai
        NotificationService::stockCountCompleted($hitung->hitung_number);

        $this->alert('success', $hitung->action . ' berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.hitung.rincian', [
            'logName' => Activity::inLog('hitungs')->where('subject_id', $this->hitung_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
