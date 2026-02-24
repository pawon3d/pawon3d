<?php

namespace App\Livewire\Setting;

use App\Models\StoreDocument;
use App\Models\StoreProfile as StoreProfileModel;
use App\Traits\CompressesImages;
use Flux\Flux;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class StoreProfile extends Component
{
    use CompressesImages, LivewireAlert, WithFileUploads, WithPagination;

    // Sort
    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    // Image previews
    public ?string $previewLogoImage = null;

    public ?string $previewBannerImage = null;

    public ?string $previewProductImage = null;

    public ?string $previewBuildingImage = null;

    // File uploads
    public mixed $logo = null;

    public mixed $banner = null;

    public mixed $productImage = null;

    public mixed $building = null;

    // Profile info
    public ?string $name = null;

    public ?string $tagline = null;

    public ?string $type = null;

    public ?string $product = null;

    public ?string $description = null;

    // Address & Contact
    public ?string $location = null;

    public ?string $address = null;

    public ?string $contact = null;

    public ?string $email = null;

    public ?string $website = null;

    // Social media
    public ?string $social_instagram = null;

    public ?string $social_facebook = null;

    public ?string $social_whatsapp = null;

    // Document modal
    public ?string $documentName = null;

    public mixed $documentFile = null;

    public ?string $documentNumber = null;

    public ?string $validFrom = null;

    public ?string $validUntil = null;

    public ?string $documentId = null;

    public bool $edit = false;

    public bool $showModal = false;

    public function mount(): void
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

    public function addModal(): void
    {
        $this->reset(['documentName', 'documentFile', 'documentNumber', 'validFrom', 'validUntil', 'edit', 'documentId']);
        $this->showModal = true;
    }

    public function editModal(string $id): void
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

    public function delete(): void
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

    public function storeDocument(): void
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

    public function updateDocument(): void
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

    public function updatedLogo(): void
    {
        $this->validate([
            'logo' => 'image|max:2048|mimes:png',
        ]);
        $this->previewLogoImage = $this->logo->temporaryUrl();
    }

    public function updatedBanner(): void
    {
        $this->validate([
            'banner' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewBannerImage = $this->banner->temporaryUrl();
    }

    public function updatedProductImage(): void
    {
        $this->validate([
            'productImage' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewProductImage = $this->productImage->temporaryUrl();
    }

    public function updatedBuilding(): void
    {
        $this->validate([
            'building' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewBuildingImage = $this->building->temporaryUrl();
    }

    public function updateStore(): mixed
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
                $storeProfile->logo = $this->storeAsWebP($this->logo, 'store_profiles');
            }

            // Banner
            if ($this->banner) {
                if ($storeProfile->banner) {
                    $oldPath = public_path('storage/'.$storeProfile->banner);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->banner = $this->storeAsWebP($this->banner, 'store_profiles');
            }

            // Product Image
            if ($this->productImage) {
                if ($storeProfile->product_image) {
                    $oldPath = public_path('storage/'.$storeProfile->product_image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->product_image = $this->storeAsWebP($this->productImage, 'store_profiles');
            }

            // Building
            if ($this->building) {
                if ($storeProfile->building) {
                    $oldPath = public_path('storage/'.$storeProfile->building);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $storeProfile->building = $this->storeAsWebP($this->building, 'store_profiles');
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

        return null;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.setting.store-profile', [
            'storeDocuments' => StoreDocument::orderBy($this->sortField, $this->sortDirection)->paginate(10),
        ]);
    }
}
