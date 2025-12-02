<?php

namespace App\Livewire\Landing;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.landing.layout')]
class Faq extends Component
{
    public array $faqs = [];

    public function mount(): void
    {
        $this->faqs = [
            [
                'question' => 'Bagaimana cara melakukan pemesanan di Pawon3D?',
                'answer' => 'Pemesanan dapat dilakukan melalui WhatsApp atau langsung datang ke tempat kami. Cukup kirim pesan berisi jenis kue, jumlah pesanan, dan waktu pengambilan atau pengantaran.',
            ],
            [
                'question' => 'Kapan saya dapat melakukan pemesanan?',
                'answer' => 'Pemesanan dapat dilakukan setiap hari selama jam operasional. Untuk kue khusus atau custom, sebaiknya pesan minimal 1–2 hari sebelumnya agar hasil maksimal.',
            ],
            [
                'question' => 'Kapan waktu pengiriman pesanan?',
                'answer' => 'Pengiriman dilakukan setiap hari mulai pukul 09:00–16:00 WIB.',
            ],
            [
                'question' => 'Kapan jam operasional Pawon3D?',
                'answer' => 'Kami buka di hari Senin-Sabtu pukul 08:00–17:00 WIB. Pemesanan online tetap bisa dilakukan di luar jam tersebut dan akan kami proses keesokan harinya.',
            ],
            [
                'question' => 'Di mana saja cakupan pengiriman Pawon3D?',
                'answer' => 'Kami melayani pengiriman di area Muara Bulian dan sekitarnya (terbatas 2 km) melalui kurir Pawon3D.',
            ],
            [
                'question' => 'Apakah Pawon3D melayani pengiriman ke luar kota?',
                'answer' => 'Untuk saat ini, kami belum melayani pengiriman luar kota, karena menjaga kualitas dan kesegaran produk.',
            ],
            [
                'question' => 'Apakah produk Pawon3D sudah halal?',
                'answer' => 'Ya, seluruh bahan yang digunakan 100% halal dan aman dikonsumsi. Beberapa produk kami sudah halal, namun beberapa produk lainnya sedang dalam proses pendaftaran sertifikat halal.',
            ],
            [
                'question' => 'Berapa lama kue Pawon3D bisa disimpan?',
                'answer' => 'Kue sebaiknya dikonsumsi dalam 2–3 hari setelah diterima. Simpan di tempat sejuk atau kulkas untuk menjaga kesegarannya. Produk tidak akan bertahan lebih dari 7 hari.',
            ],
            [
                'question' => 'Apakah Pawon3D melayani pesanan untuk acara atau snack box?',
                'answer' => 'Ya, kami melayani pesanan snack box, coffee break, dan hampers untuk berbagai acara, baik pribadi maupun perusahaan.',
            ],
            [
                'question' => 'Apakah ada program pelanggan di Pawon3D?',
                'answer' => 'Ya, pembeli dapat menjadi pelanggan Pawon3D untuk mendapatkan poin. Pendaftaran bisa dilakukan saat melakukan pemesanan di kasir.',
            ],
            [
                'question' => 'Bagaimana cara mengecek dan menggunakan poin?',
                'answer' => 'Poin dapat dilihat di struk atau dikonfirmasi melalui kasir. Poin dapat digunakan sebagai potongan harga pada pembelian berikutnya.',
            ],
            [
                'question' => 'Bagaimana jika ingin berhenti menjadi member Pawon3D?',
                'answer' => 'Cukup hubungi Pawon3D dan akun pelanggan Anda akan dihapus sesuai permintaan.',
            ],
        ];

        View::share('title', 'FAQ - Pawon3D');
    }

    public function render()
    {
        return view('livewire.landing.faq');
    }
}
