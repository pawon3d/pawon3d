<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ResetProductStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:reset-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset semua stok produk ke 0 untuk hari baru';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Mereset stok produk...');

        $count = Product::where('stock', '>', 0)->count();

        Product::query()->update(['stock' => 0]);

        $this->info("Selesai. {$count} produk telah direset stoknya ke 0.");

        return self::SUCCESS;
    }
}
