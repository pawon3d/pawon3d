<?php

namespace App\Livewire\Setting;

use Flux\Flux;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class StoreProfile extends Component
{
    use WithFileUploads, LivewireAlert;

    public $previewLogoImage, $previewBannerImage, $previewBuildingImage;
    public $logo, $banner, $building;
    public $name, $tagline, $type;
    public $product, $description;
    public $location, $address, $contact, $email, $website;
    public $is_senin, $open_senin, $close_senin;
    public $is_selasa, $open_selasa, $close_selasa;
    public $is_rabu, $open_rabu, $close_rabu;
    public $is_kamis, $open_kamis, $close_kamis;
    public $is_jumat, $open_jumat, $close_jumat;
    public $is_sabtu, $open_sabtu, $close_sabtu;
    public $is_minggu, $open_minggu, $close_minggu;
    public $social_instagram, $social_facebook, $social_whatsapp;

    public $sortDirection = 'desc';
    public $sortField = 'created_at';
    public $storeDocuments = [];
    public $documentName, $documentFile, $documentNumber, $validFrom, $validUntil;
    public $documentId, $edit = false;
    public $showModal = false;

    public function mount()
    {
        View::share('title', 'Profil Toko');
        View::share('mainTitle', 'Pengaturan');
        $storeProfile = \App\Models\StoreProfile::first();
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
        if ($storeProfile) {
            // $this->logo = $storeProfile->logo;
            // $this->banner = $storeProfile->banner;
            // $this->building = $storeProfile->building;
            $this->name = $storeProfile->name;
            $this->tagline = $storeProfile->tagline;
            $this->type = $storeProfile->type;
            $this->product = $storeProfile->product;
            $this->description = $storeProfile->description;
            $this->location = $storeProfile->location;
            $this->address = $storeProfile->address;
            $this->contact = $storeProfile->contact;
            $this->email = $storeProfile->email;
            $this->website = $storeProfile->website;

            // Jam buka tutup
            $this->is_senin = (bool) $storeProfile->is_senin;
            $this->open_senin = $storeProfile->open_senin;
            $this->close_senin = $storeProfile->close_senin;

            // Selasa
            $this->is_selasa = (bool) $storeProfile->is_selasa;
            $this->open_selasa = $storeProfile->open_selasa;
            $this->close_selasa = $storeProfile->close_selasa;

            // Rabu
            $this->is_rabu = (bool) $storeProfile->is_rabu;
            $this->open_rabu = $storeProfile->open_rabu;
            $this->close_rabu = $storeProfile->close_rabu;

            // Kamis
            $this->is_kamis = (bool) $storeProfile->is_kamis;
            $this->open_kamis = $storeProfile->open_kamis;
            $this->close_kamis = $storeProfile->close_kamis;

            // Jumat
            $this->is_jumat = (bool) $storeProfile->is_jumat;
            $this->open_jumat = $storeProfile->open_jumat;
            $this->close_jumat = $storeProfile->close_jumat;

            // Sabtu
            $this->is_sabtu = (bool) $storeProfile->is_sabtu;
            $this->open_sabtu = $storeProfile->open_sabtu;
            $this->close_sabtu = $storeProfile->close_sabtu;

            // Minggu
            $this->is_minggu = (bool) $storeProfile->is_minggu;
            $this->open_minggu = $storeProfile->open_minggu;
            $this->close_minggu = $storeProfile->close_minggu;

            // Sosial media
            $this->social_instagram = $storeProfile->social_instagram;
            $this->social_facebook = $storeProfile->social_facebook;
            $this->social_whatsapp = $storeProfile->social_whatsapp;

            // Preview images
            $this->previewLogoImage = $storeProfile->logo ? env('APP_URL') . '/storage/' . $storeProfile->logo : null;
            $this->previewBannerImage = $storeProfile->banner ? env('APP_URL') . '/storage/' . $storeProfile->banner : null;
            $this->previewBuildingImage = $storeProfile->building ? env('APP_URL') . '/storage/' . $storeProfile->building : null;
        } else {
            $this->previewLogoImage = null;
            $this->previewBannerImage = null;
            $this->previewBuildingImage = null;
        }
        $storeDocuments = \App\Models\StoreDocument::orderBy($this->sortField, $this->sortDirection)->get();
        if ($storeDocuments->count() > 0) {
            $this->storeDocuments = $storeDocuments;
        } else {
            $this->storeDocuments = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function addModal()
    {
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
        $this->showModal = true;
    }
    public function editModal($id)
    {
        $this->edit = true;
        $document = \App\Models\StoreDocument::findOrFail($id);
        $this->documentId = $document->id;
        $this->documentName = $document->document_name;
        $this->documentFile = $document->document_file;
        $this->documentNumber = $document->document_number;
        $this->validFrom = $document->valid_from;
        $this->validUntil = $document->valid_until;
        $this->showModal = true;
    }

    public function delete()
    {
        $document = \App\Models\StoreDocument::findOrFail($this->documentId);
        if ($document->document_file) {
            $oldFilePath = public_path('storage/' . $document->document_file);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
        $document->delete();
        $this->alert('success', 'Dokumen berhasil dihapus!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
        Flux::modals()->close();
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
        $this->storeDocuments = \App\Models\StoreDocument::orderBy($this->sortField, $this->sortDirection)->get();
    }
    public function storeDocument()
    {
        $this->validate([
            'documentName' => 'required|string|max:255',
            'documentFile' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png',
            'documentNumber' => 'nullable|string|max:255',
            'validFrom' => 'nullable|date',
            'validUntil' => 'nullable|date|after_or_equal:validFrom',
        ]);

        $storeDocument = new \App\Models\StoreDocument();
        $storeDocument->document_name = $this->documentName;
        $storeDocument->document_number = $this->documentNumber;
        $storeDocument->valid_from = $this->validFrom;
        $storeDocument->valid_until = $this->validUntil;

        if ($this->documentFile) {
            // Hapus file lama jika ada
            if ($storeDocument->document_file) {
                $oldFilePath = public_path('storage/' . $storeDocument->document_file);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            $storeDocument->document_file = $this->documentFile->store('store_documents', 'public');
        }

        $storeDocument->save();
        $this->alert('success', 'Dokumen berhasil disimpan!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
        $this->showModal = false;

        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
        $this->storeDocuments = \App\Models\StoreDocument::orderBy($this->sortField, $this->sortDirection)->get();
    }
    public function updateDocument()
    {
        $this->validate([
            'documentName' => 'required|string|max:255',
            'documentFile' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png',
            'documentNumber' => 'nullable|string|max:255',
            'validFrom' => 'nullable|date',
            'validUntil' => 'nullable|date|after_or_equal:validFrom',
        ]);

        $storeDocument = \App\Models\StoreDocument::findOrFail($this->documentId);
        $storeDocument->document_name = $this->documentName;
        $storeDocument->document_number = $this->documentNumber;
        $storeDocument->valid_from = $this->validFrom;
        $storeDocument->valid_until = $this->validUntil;

        if ($this->documentFile instanceof \Illuminate\Http\UploadedFile) {
            // Hapus file lama jika ada
            if ($storeDocument->document_file) {
                $oldFilePath = public_path('storage/' . $storeDocument->document_file);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            $storeDocument->document_file = $this->documentFile->store('store_documents', 'public');
        }

        $storeDocument->save();
        $this->alert('success', 'Dokumen berhasil diperbarui!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
        $this->showModal = false;
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
        $this->storeDocuments = \App\Models\StoreDocument::orderBy($this->sortField, $this->sortDirection)->get();
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewLogoImage = $this->logo->temporaryUrl();
    }
    public function updatedBanner()
    {
        $this->validate([
            'banner' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewBannerImage = $this->banner->temporaryUrl();
    }
    public function updatedBuilding()
    {
        $this->validate([
            'building' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewBuildingImage = $this->building->temporaryUrl();
    }

    public function updateStore()
    {
        \App\Models\StoreProfile::updateOrCreate(
            [],
            [
                'name' => $this->name,
                'tagline' => $this->tagline,
                'type' => $this->type,
                'product' => $this->product,
                'description' => $this->description,
                'location' => $this->location,
                'address' => $this->address,
                'contact' => $this->contact,
                'email' => $this->email,
                'website' => $this->website,

                // Jam buka tutup
                'is_senin' => (bool) $this->is_senin,
                'open_senin' => $this->open_senin,
                'close_senin' => $this->close_senin,

                // Selasa
                'is_selasa' => (bool) $this->is_selasa,
                'open_selasa' => $this->open_selasa,
                'close_selasa' => $this->close_selasa,

                // Rabu
                'is_rabu' => (bool) $this->is_rabu,
                'open_rabu' => $this->open_rabu,
                'close_rabu' => $this->close_rabu,

                // Kamis
                'is_kamis' => (bool) $this->is_kamis,
                'open_kamis' => $this->open_kamis,
                'close_kamis' => $this->close_kamis,

                // Jumat
                'is_jumat' => (bool) $this->is_jumat,
                'open_jumat' => $this->open_jumat,
                'close_jumat' => $this->close_jumat,
                // Sabtu
                'is_sabtu' => (bool) $this->is_sabtu,
                'open_sabtu' => $this->open_sabtu,
                'close_sabtu' => $this->close_sabtu,
                // Minggu
                'is_minggu' => (bool) $this->is_minggu,
                'open_minggu' => $this->open_minggu,
                'close_minggu' => $this->close_minggu,
                // Sosial media
                'social_instagram' => $this->social_instagram,
                'social_facebook' => $this->social_facebook,
                'social_whatsapp' => $this->social_whatsapp,
            ]
        );
        // Hapus gambar lama jika ada
        $storeProfile = \App\Models\StoreProfile::first();
        if ($storeProfile) {
            if ($this->logo && $storeProfile->logo) {
                $oldLogoPath = public_path('storage/' . $storeProfile->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            if ($this->banner && $storeProfile->banner) {
                $oldBannerPath = public_path('storage/' . $storeProfile->banner);
                if (file_exists($oldBannerPath)) {
                    unlink($oldBannerPath);
                }
            }
            if ($this->building && $storeProfile->building) {
                $oldBuildingPath = public_path('storage/' . $storeProfile->building);
                if (file_exists($oldBuildingPath)) {
                    unlink($oldBuildingPath);
                }
            }
            $storeProfile->banner = $this->banner ? $this->banner->store('store_profiles', 'public') : $storeProfile->banner;
            $storeProfile->building = $this->building ? $this->building->store('store_profiles', 'public') : $storeProfile->building;
            $storeProfile->logo = $this->logo ? $this->logo->store('store_profiles', 'public') : $storeProfile->logo;
            $storeProfile->save();
        }
        if ($this->logo) {
            return redirect()->route('profil-usaha')->with('success', 'Profil Toko berhasil diperbarui!');
        } else {
            $this->alert('success', 'Profil Toko berhasil diperbarui!', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'showConfirmButton' => false,
            ]);
        }
    }
    public function render()
    {
        return view('livewire.setting.store-profile');
    }
}