<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class HandleTrialExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:handle-trial-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired trial tenants and deactivate them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredTenants = Tenant::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->where('is_active', true)
            ->whereDoesntHave('subscriptions', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        if ($expiredTenants->isEmpty()) {
            $this->info('No expired trial tenants found.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expiredTenants as $tenant) {
            $tenant->update(['is_active' => false]);
            $count++;
            $this->line("  ✗ Deactivated tenant: {$tenant->name} (ID: {$tenant->id})");
        }

        $this->info("Processed {$count} expired trial tenant(s).");

        return self::SUCCESS;
    }
}
