<?php

namespace App\Livewire\Setting;

use App\Models\StoreDocument;
use App\Models\StoreProfile as StoreProfileModel;
use Flux\Flux;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class StoreProfile extends Component
{
    use LivewireAlert, WithFileUploads, WithPagination;

    // Image previews
    public $previewLogoImage;

    public $previewBannerImage;

    public $previewProductImage;

    public $previewBuildingImage;

    // File uploads
    public $logo;

    public $banner;

    public $productImage;

    public $building;

    // Profile info
    public $name;

    public $tagline;

    public $type;

    public $product;

    public $description;

    // Address & Contact
    public $location;

    public $address;

    public $contact;

    public $email;

    public $website;

    // Social media
    public $social_instagram;

    public $social_facebook;

    public $social_whatsapp;

    // Document modal

    public $documentName;

    public $documentFile;

    public $documentNumber;

    public $validFrom;

    public $validUntil;

    public $documentId;

    public $edit = false;

    public $showModal = false;

    public function mount()
    {
        View::share('title', 'Profil Usaha');
        View::share('mainTitle', 'Pengaturan');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }

        $storeProfile = StoreProfileModel::first();

        if ($storeProfile) {
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

            // Sosial media
            $this->social_instagram = $storeProfile->social_instagram;
            $this->social_facebook = $storeProfile->social_facebook;
            $this->social_whatsapp = $storeProfile->social_whatsapp;

            // Preview images
            $this->previewLogoImage = $storeProfile->logo ? env('APP_URL').'/storage/'.$storeProfile->logo : null;
            $this->previewBannerImage = $storeProfile->banner ? env('APP_URL').'/storage/'.$storeProfile->banner : null;
            $this->previewProductImage = $storeProfile->product_image ? env('APP_URL').'/storage/'.$storeProfile->product_image : null;
            $this->previewBuildingImage = $storeProfile->building ? env('APP_URL').'/storage/'.$storeProfile->building : null;
        } else {
            $this->previewLogoImage = null;
            $this->previewBannerImage = null;
            $this->previewProductImage = null;
            $this->previewBuildingImage = null;
        }
    }

    public function addModal()
    {
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit', 'documentId']);
        $this->showModal = true;
    }

    public function editModal($id)
    {
        $this->edit = true;
        $document = StoreDocument::findOrFail($id);
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
        $document = StoreDocument::findOrFail($this->documentId);
        if ($document->document_file) {
            $oldFilePath = public_path('storage/'.$document->document_file);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
        $document->delete();
        $this->alert('success', 'Dokumen berhasil dihapus!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
        Flux::modals()->close();
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
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

        $storeDocument = new StoreDocument;
        $storeDocument->document_name = $this->documentName;
        $storeDocument->document_number = $this->documentNumber;
        $storeDocument->valid_from = $this->validFrom;
        $storeDocument->valid_until = $this->validUntil;

        if ($this->documentFile) {
            $storeDocument->document_file = $this->documentFile->store('store_documents', 'public');
        }

        $storeDocument->save();
        $this->alert('success', 'Dokumen berhasil disimpan!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
        $this->showModal = false;
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
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

        $storeDocument = StoreDocument::findOrFail($this->documentId);
        $storeDocument->document_name = $this->documentName;
        $storeDocument->document_number = $this->documentNumber;
        $storeDocument->valid_from = $this->validFrom;
        $storeDocument->valid_until = $this->validUntil;

        if ($this->documentFile instanceof \Illuminate\Http\UploadedFile) {
            // Hapus file lama jika ada
            if ($storeDocument->document_file) {
                $oldFilePath = public_path('storage/'.$storeDocument->document_file);
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
        ]);
        $this->showModal = false;
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit']);
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:2048|mimes:png',
        ]);
        $this->previewLogoImage = $this->logo->temporaryUrl();
    }

    public function updatedBanner()
    {
        $this->validate([
            'banner' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewBannerImage = $this->banner->temporaryUrl();
    }

    public function updatedProductImage()
    {
        $this->validate([
            'productImage' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewProductImage = $this->productImage->temporaryUrl();
    }

    public function updatedBuilding()
    {
        $this->validate([
            'building' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewBuildingImage = $this->building->temporaryUrl();
    }

    public function updateStore()
    {
        StoreProfileModel::updateOrCreate(
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
                'social_instagram' => $this->social_instagram,
                'social_facebook' => $this->social_facebook,
                'social_whatsapp' => $this->social_whatsapp,
            ]
        );

        // Handle image uploads
        $storeProfile = StoreProfileModel::first();

        if ($storeProfile) {
            // Logo
            if ($this->logo) {
                if ($storeProfile->logo) {
                    $oldPath = public_path('storage/'.$storeProfile->logo);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->logo = $this->logo->store('store_profiles', 'public');
            }

            // Banner
            if ($this->banner) {
                if ($storeProfile->banner) {
                    $oldPath = public_path('storage/'.$storeProfile->banner);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->banner = $this->banner->store('store_profiles', 'public');
            }

            // Product Image
            if ($this->productImage) {
                if ($storeProfile->product_image) {
                    $oldPath = public_path('storage/'.$storeProfile->product_image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->product_image = $this->productImage->store('store_profiles', 'public');
            }

            // Building
            if ($this->building) {
                if ($storeProfile->building) {
                    $oldPath = public_path('storage/'.$storeProfile->building);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->building = $this->building->store('store_profiles', 'public');
            }

            $storeProfile->save();
        }

        if ($this->logo || $this->banner || $this->productImage || $this->building) {
            return redirect()->route('profil-usaha')->with('success', 'Profil Usaha berhasil diperbarui!');
        }

        $this->alert('success', 'Profil Usaha berhasil diperbarui!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.setting.store-profile', [
            'storeDocuments' => StoreDocument::orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
