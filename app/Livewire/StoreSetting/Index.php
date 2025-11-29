<?php

namespace App\Livewire\StoreSetting;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use LivewireAlert, WithFileUploads;

    public $storeName;

    public $contact;

    public $address;

    public $heroImage;

    public $heroTitle;

    public $heroSubtitle;

    public $logo;

    public $previewLogo = null;

    public $previewHeroImage = null;

    public function mount()
    {
        View::share('title', 'Pengaturan Toko');

        $storeSetting = \App\Models\StoreSetting::first();
        if ($storeSetting) {
            $this->storeName = $storeSetting->store_name;
            $this->contact = $storeSetting->contact;
            $this->address = $storeSetting->address;
            $this->previewHeroImage = $storeSetting->hero_image;
            $this->heroTitle = $storeSetting->hero_title;
            $this->heroSubtitle = $storeSetting->hero_subtitle;
            $this->previewLogo = $storeSetting->logo;
        } else {
            $this->storeName = 'Pawon3D';
            $this->contact = '628123456789';
            $this->address = '';
            $this->previewHeroImage = '/assets/images/homepage/hero.jpeg';
            $this->heroTitle = 'Ciptakan Momen Manis dengan Kue Istimewa';
            $this->heroSubtitle = 'Temukan berbagai pilihan kue dan camilan, mulai dari snack untuk tahlilan hingga kue ulang tahun, yang dibuat dengan resep rahasia dan bahan berkualitas. Pesan dalam jumlah besar untuk setiap acara spesial Anda.';
            $this->previewLogo = '';
        }
    }

    public function save()
    {
        $storeSetting = \App\Models\StoreSetting::first();

        if ($storeSetting) {
            $storeSetting->update([
                'store_name' => $this->storeName,
                'contact' => $this->contact,
                'address' => $this->address,
                'hero_title' => $this->heroTitle,
                'hero_subtitle' => $this->heroSubtitle,
            ]);
        } else {
            $storeSetting = \App\Models\StoreSetting::create([
                'store_name' => $this->storeName,
                'contact' => $this->contact,
                'address' => $this->address,
                'hero_title' => $this->heroTitle,
                'hero_subtitle' => $this->heroSubtitle,
                'hero_image' => null,
                'logo' => null,
            ]);
        }

        if ($this->heroImage) {
            if ($storeSetting->hero_image) {
                Storage::disk('public')->delete($storeSetting->hero_image);
            }
            $storeSetting->hero_image = $this->heroImage->store('hero_images', 'public');
            $storeSetting->save();
            $this->previewHeroImage = Storage::url($storeSetting->hero_image);
        } else {
            $this->previewHeroImage = $storeSetting->hero_image;
        }

        if ($this->logo) {
            if ($storeSetting->logo) {
                Storage::disk('public')->delete($storeSetting->logo);
            }
            $storeSetting->logo = $this->logo->store('logos', 'public');
            $storeSetting->save();
            $this->previewLogo = Storage::url($storeSetting->logo);
        } else {
            $this->previewLogo = $storeSetting->logo;
        }

        $this->alert('success', 'Pengaturan Toko berhasil disimpan');

        $this->reset(['heroImage', 'logo']);
    }

    public function render()
    {
        return view('livewire.store-setting.index');
    }

    public function updatedHeroImage()
    {
        $this->previewHeroImage = $this->heroImage->temporaryUrl();
    }

    public function updatedLogo()
    {
        $this->previewLogo = $this->logo->temporaryUrl();
    }
}
