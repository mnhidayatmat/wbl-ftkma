<?php

namespace App\Console\Commands;

use App\Models\CompanyAgreement;
use Illuminate\Console\Command;

class UpdateExpiredAgreements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agreements:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of expired agreements to "Expired"';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for expired agreements...');

        // Get all active agreements with end dates
        $activeAgreements = CompanyAgreement::where('status', 'Active')
            ->whereNotNull('end_date')
            ->get();

        $updatedCount = 0;

        foreach ($activeAgreements as $agreement) {
            if ($agreement->isExpired()) {
                $agreement->update(['status' => 'Expired']);
                $updatedCount++;

                $this->line("✓ Updated: {$agreement->company->company_name} - {$agreement->agreement_type} (Expired on {$agreement->end_date->format('Y-m-d')})");
            }
        }

        if ($updatedCount > 0) {
            $this->info("Successfully updated {$updatedCount} expired agreement(s).");
        } else {
            $this->info('No expired agreements found.');
        }

        return Command::SUCCESS;
    }
}
