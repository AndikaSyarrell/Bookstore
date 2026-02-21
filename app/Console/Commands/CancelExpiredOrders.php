<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RefundService;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     */
    protected $description = 'Auto-cancel orders that have not been paid within 3 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired orders...');

        try {
            $processed = RefundService::processExpiredOrders();

            $this->info("✓ Successfully auto-cancelled {$processed} expired orders");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to process expired orders: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}