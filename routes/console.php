<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek stok bahan yang akan habis dan kadaluarsa setiap hari jam 00:00
Schedule::command('inventory:check-alerts')->dailyAt('00:00');

// Reset stok produk ke 0 setiap pergantian hari (jam 00:00)
Schedule::command('product:reset-stock')->dailyAt('00:00');

