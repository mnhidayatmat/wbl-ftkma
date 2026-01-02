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
    protected $signature = 'agreements:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update agreement statuses dynamically based on dates (Active, Near Expiry, Expired, Not Started)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking agreement statuses based on dates...');

        // Get all agreements that can have dynamic status changes
        // Exclude Draft, Pending, and Terminated as they are manually managed
        $agreements = CompanyAgreement::whereNotIn('status', ['Draft', 'Pending', 'Terminated'])
            ->whereNotNull('start_date')
            ->get();

        $updatedCount = 0;
        $statusChanges = [];

        foreach ($agreements as $agreement) {
            $calculatedStatus = $agreement->calculateDynamicStatus();

            if ($calculatedStatus && $agreement->status !== $calculatedStatus) {
                $oldStatus = $agreement->status;
                $agreement->update(['status' => $calculatedStatus]);
                $updatedCount++;

                $statusChanges[] = [
                    'company' => $agreement->company->company_name ?? 'N/A',
                    'type' => $agreement->agreement_type,
                    'from' => $oldStatus,
                    'to' => $calculatedStatus,
                ];

                $this->line("✓ {$agreement->company->company_name} - {$agreement->agreement_type}: {$oldStatus} → {$calculatedStatus}");
            }
        }

        if ($updatedCount > 0) {
            $this->newLine();
            $this->info("Successfully updated {$updatedCount} agreement(s).");
        } else {
            $this->info('All agreement statuses are up to date.');
        }

        return Command::SUCCESS;
    }
}
