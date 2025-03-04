<?php

namespace App\Livewire\ProcessedMaterial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProcessedMaterial;
use App\Models\Material;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $name, $quantity, $delete_id;
    public $processedMaterialDetails = [];
    public $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDetailModal = false;
    public $selectedMaterial = null;
    public $editId = null;
    public $detailData = null;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'name' => 'required|string',
        'quantity' => 'required|integer',
        'processedMaterialDetails' => 'required|array|min:1',
        'processedMaterialDetails.*.material_id' => 'required|exists:materials,id',
        'processedMaterialDetails.*.material_quantity' => 'required|integer',
        'processedMaterialDetails.*.material_unit' => 'required|string',
    ];

    public function mount()
    {
        View::share('title', 'Bahan Baku Olahan');

        $this->addDetailRow();
    }

    public function render()
    {
        return view('livewire.processed-material.index', [
            'processedMaterials' => ProcessedMaterial::with('processed_material_details.material')
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->paginate(10),
            'materials' => Material::all()
        ]);
    }

    public function addDetailRow()
    {
        $this->processedMaterialDetails[] = [
            'material_id' => '',
            'material_quantity' => 0,
            'material_unit' => ''
        ];
    }

    public function removeDetailRow($index)
    {
        unset($this->processedMaterialDetails[$index]);
        $this->processedMaterialDetails = array_values($this->processedMaterialDetails);
    }

    public function setMaterial($index, $materialId)
    {
        if (!$materialId) return;

        $material = Material::findOrFail($materialId);
        $this->processedMaterialDetails[$index] = [
            'material_id' => $material->id,
            'material_quantity' => $this->processedMaterialDetails[$index]['material_quantity'] ?? 0,
            'material_unit' => $material->unit
        ];
    }

    public function store()
    {
        $this->validate();

        // Filter detail yang memiliki material_id valid
        $validDetails = array_filter($this->processedMaterialDetails, function ($detail) {
            return !empty($detail['material_id']);
        });

        if (count($validDetails) < 1) {
            $this->addError('processedMaterialDetails', 'Minimal 1 bahan baku harus dipilih');
            return;
        }

        $processedMaterial = ProcessedMaterial::create([
            'name' => $this->name,
            'quantity' => $this->quantity
        ]);

        foreach ($validDetails as $detail) { // Gunakan $validDetails
            $processedMaterial->processed_material_details()->create($detail);
        }

        $this->resetForm();
        $this->showAddModal = false;
        $this->alert('success', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $processedMaterial = ProcessedMaterial::with('processed_material_details')->find($id);
        $this->editId = $id;
        $this->name = $processedMaterial->name;
        $this->quantity = $processedMaterial->quantity;
        $this->processedMaterialDetails = $processedMaterial->processed_material_details->toArray();
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();

        $validDetails = array_filter($this->processedMaterialDetails, function ($detail) {
            return !empty($detail['material_id']);
        });

        if (count($validDetails) < 1) {
            $this->addError('processedMaterialDetails', 'Minimal 1 bahan baku harus dipilih');
            return;
        }

        $processedMaterial = ProcessedMaterial::find($this->editId);
        $processedMaterial->update([
            'name' => $this->name,
            'quantity' => $this->quantity
        ]);

        $processedMaterial->processed_material_details()->delete();
        foreach ($validDetails as $detail) {
            $processedMaterial->processed_material_details()->create($detail);
        }

        $this->resetForm();
        $this->showEditModal = false;
        $this->alert('success', 'Data berhasil diperbarui!');
    }

    public function confirmDelete($id)
    {

        // Simpan ID ke dalam properti
        $this->delete_id = $id;

        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus olahan ini?', [
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

        $delete = ProcessedMaterial::find($this->delete_id);

        if ($delete) {
            $delete->delete();
            $this->alert('success', 'Olahan berhasil dihapus!');
        } else {
            $this->alert('error', 'Olahan tidak ditemukan!');
        }

        // Reset setelah dihapus
        $this->reset('delete_id');
    }

    public function showDetail($id)
    {
        $this->detailData = ProcessedMaterial::with('processed_material_details.material')->find($id);
        $this->showDetailModal = true;
    }

    private function resetForm()
    {
        $this->reset([
            'name',
            'quantity',
            'processedMaterialDetails',
            'editId',
            'selectedMaterial'
        ]);
        $this->processedMaterialDetails = [
            [
                'material_id' => '',
                'material_quantity' => 0,
                'material_unit' => ''
            ]
        ];
        $this->resetErrorBag();
    }
}