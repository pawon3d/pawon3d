<?php

namespace App\Console\Commands;

use App\Models\TransactionDetail;
use Illuminate\Console\Command;

class UpdateExistingCapitalSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:update-capital-snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pcs_capital_snapshot for existing transaction details using current product capital';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating pcs_capital_snapshot for existing transaction details...');

        // Get all transaction details where pcs_capital_snapshot is 0
        $details = TransactionDetail::where('pcs_capital_snapshot', 0)
            ->with('product')
            ->get();

        $updated = 0;
        $bar = $this->output->createProgressBar($details->count());

        foreach ($details as $detail) {
            if ($detail->product) {
                $detail->pcs_capital_snapshot = $detail->product->pcs_capital ?? 0;
                $detail->save();
                $updated++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully updated {$updated} transaction details.");

        return Command::SUCCESS;
    }
}
