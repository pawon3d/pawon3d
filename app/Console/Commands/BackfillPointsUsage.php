<?php

namespace App\Console\Commands;

use App\Models\PointsHistory;
use App\Models\Transaction;
use Illuminate\Console\Command;

class BackfillPointsUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'points:backfill-usage {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing points usage entries for transactions that used points but have no PointsHistory entry.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Scanning transactions for missing points usage...');

        $transactions = Transaction::where('points_used', '>', 0)->get();

        $created = 0;

        foreach ($transactions as $trx) {
            $exists = PointsHistory::where('transaction_id', $trx->id)
                ->where('action', 'Tukar Poin')
                ->where('points', '<', 0)
                ->exists();

            if (! $exists) {
                $this->line("Transaction {$trx->invoice_number} missing points history. Points used: {$trx->points_used}");
                if (! $dryRun) {
                    PointsHistory::create([
                        'phone' => $trx->phone,
                        'action' => 'Tukar Poin',
                        'points' => -1 * $trx->points_used,
                        'transaction_id' => $trx->id,
                    ]);
                    $created++;
                }
            }
        }

        $this->info('Done. Created: '.$created);

        return 0;
    }
}
