<?php

namespace App\Livewire\Hitung;

use App\Models\Hitung;
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
        View::share('title', 'Rincian '.$hitung->action);
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

        $this->activityLogs = $logs->map(fn ($log) => [
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
            $hitung->delete();

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
        unset($this->hitung); // Clear computed property cache
        $this->alert('success', $hitung->action.' berhasil dimulai.');
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
        $hitung->details->each(function ($detail) use ($hitung) {
            $batch = $detail->materialBatch;
            if (! $batch) {
                return;
            }

            if ($hitung->action === 'Hitung Persediaan') {
                // Untuk Hitung Persediaan:
                // quantity_actual = jumlah yang dihitung secara fisik
                // quantity_expect = jumlah yang diharapkan (dari batch_quantity sebelumnya)
                // Selisih = quantity_actual - quantity_expect
                // Jika selisih negatif = kekurangan (batch berkurang)
                // Jika selisih positif = kelebihan (batch bertambah)
                // Update batch_quantity menjadi quantity_actual (hasil hitung fisik)
                $batch->update([
                    'batch_quantity' => $detail->quantity_actual,
                ]);
            } else {
                // Untuk Catat Persediaan Rusak / Hilang:
                // quantity_actual = jumlah yang rusak/hilang
                // Kurangi batch_quantity sebesar quantity_actual
                $newBatchQuantity = max(0, $batch->batch_quantity - $detail->quantity_actual);

                // Jika batch quantity menjadi 0 dan batch sudah expired, hapus batch
                $isExpired = $batch->date && now()->greaterThan($batch->date);

                if ($newBatchQuantity <= 0 && $isExpired) {
                    $batch->delete();
                } else {
                    $batch->update([
                        'batch_quantity' => $newBatchQuantity,
                    ]);
                }
            }
        });

        unset($this->hitung); // Clear computed property cache
        $this->alert('success', $hitung->action.' berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.hitung.rincian', [
            'logName' => Activity::inLog('hitungs')->where('subject_id', $this->hitung_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
